# Architecture

## System Overview

Instructor Ledger is a multi-instructor revenue sharing platform. When a student purchases a subscription, the platform:

1. Deducts a 20% platform fee from the subscription amount.
2. Distributes the remaining revenue across instructors according to their configured percentage shares.
3. Holds distributed amounts in each instructor's pending balance.
4. Processes payouts on a monthly schedule, moving funds from pending to available via a payment gateway.
5. Records every financial movement in an immutable ledger.

---

## Key Architectural Decisions

### Double-Entry Ledger

Every financial event creates a `LedgerEntry` record before any balance is mutated. This provides a full audit trail independent of the balance columns and enables reconciliation if a balance ever drifts out of sync. The `type` enum distinguishes between `revenue`, `platform_fee`, `refund`, and `payout` entries.

### Three-State Balance

`InstructorBalance` tracks funds across three columns rather than a single balance:

| Column              | Meaning |
|---------------------|---|
| `total_balance`     | Cumulative lifetime revenue allocated to the instructor |
| `pending_balance`   | Revenue allocated but not yet paid out (gateway has not confirmed) |
| `available_balance` | Revenue confirmed successfully paid out |

This makes it impossible to double-pay: the payout job only touches `pending_balance > 0`, and the debit happens atomically in the same transaction that records the gateway response.

### Service-Interface Binding

All service dependencies are declared against interfaces (`RevenueAllocationServiceInterface`, `MockPaymentGatewayServiceInterface`) and bound to their implementations in `BindServiceProvider`. This decouples consuming code from concrete implementations, making it straightforward to swap the mock gateway for a real one or to override bindings in tests.

---

## Revenue Allocation Strategy

Allocation is handled by `RevenueAllocationService::allocate()` and runs inside a database transaction immediately after a subscription is created.

**Steps:**

1. Load the subscription's `SubscriptionInstructorShare` records and validate that all percentages sum to 100.
2. Compute the platform fee: `subscription.amount × config('platform-fee.percentage')` (currently 20%).
3. Compute distributable revenue: `subscription.amount − platform_fee`.
4. For each instructor share (except the last), compute `floor(distributable × percentage / 100)`.
5. Give the remainder of any rounding discrepancy to the last instructor.
6. Create a `revenue` LedgerEntry per instructor and a `platform_fee` LedgerEntry for the platform.
7. Increment each instructor's `pending_balance` and `total_balance` atomically via `lockForUpdate()`.

Rounding is always deterministic: integer arithmetic and a single remainder assignment ensure that `sum(allocated_amounts) == distributable_revenue` with no floating-point drift.

---

## Idempotency Approach

**At the payout level**, each `Payout` record is created with a UUID `idempotency_key` before the gateway call is made. If the process-payouts command runs a second time for the same instructor before the first payout completes:

- The job acquires a Redis lock keyed to `payout:instructor:{id}`. A second invocation blocks until the lock is released.
- On re-entry after the lock is available, the job re-checks `pending_balance`. If the first run already zeroed it, the second run exits early without creating a new payout.

**At the gateway level**, the `idempotency_key` is passed to `transfer()` so that a real payment provider can deduplicate retries on their side.

**For the reconciliation flow**, a payout in `pending_verification` status is only reconciled once: the reconcile command locks the payout row, re-reads its status, and exits immediately if the status is no longer `pending_verification` (meaning another process already resolved it).

---

## Provider Timeout Handling

The payment gateway can return one of three outcomes from a `transfer()` call:

| Outcome | Action |
|---|---|
| `'success'` | Mark payout `success`, debit `pending_balance`, credit `available_balance`, write ledger entry |
| `'failed'` | Mark payout `failed`, leave balances unchanged (funds remain in pending for manual review) |
| Exception thrown | Mark payout `pending_verification`, leave balances unchanged |

The `app:reconcile-payouts` command (runs every 30 minutes) picks up all `pending_verification` payouts and calls `verifyTransfer(idempotency_key)` on the gateway:

- If the gateway confirms `success`, the reconciler applies the balance mutation and ledger entry exactly as a direct success would have.
- If the gateway returns `failed`, the payout is marked `failed`.

This two-phase pattern means a network timeout between the application server and the gateway can never result in a double-charge: money only moves in the application's books after a confirmed gateway response.

---

## Scaling Considerations

- **Chunked processing**: `ProcessPayoutsCommand` chunks instructor balance queries in batches of 100, preventing memory exhaustion when processing large numbers of instructors.
- **Queue-based jobs**: `ProcessInstructorPayoutJob` implements `ShouldQueue`. Under load, jobs back-pressure naturally into the queue rather than blocking the scheduler process. Horizontal scaling is achieved by running additional queue workers.
- **Redis locks**: The per-instructor lock in `ProcessInstructorPayoutJob` ensures that even if multiple queue workers pick up jobs for the same instructor simultaneously (e.g. after a retry surge), only one runs at a time.
- **Database transactions with row locks**: `lockForUpdate()` on the `InstructorBalance` row within the payout transaction prevents race conditions at the database level as a second line of defense.
- **Reconciliation decoupled from payout**: Running reconciliation on a separate 30-minute schedule means a spike in gateway timeouts does not block the main payout dispatch flow.

---

## Known Limitations

- **Mock gateway only**: The current implementation uses `MockPaymentGatewayService`, which simulates randomized outcomes. Integrating a real payment provider requires implementing `MockPaymentGatewayServiceInterface` and updating the binding in `BindServiceProvider`.
- **Hardcoded instructor shares**: Subscription creation always assigns 50/30/20% splits to the first three instructors in the database. A production system needs a proper instructor-course ownership model to derive shares dynamically.
- **Hardcoded student identity**: `SubscriptionController::store()` assigns the first student user rather than the authenticated user. This must be replaced before the app handles real users.
- **No retry limit on failed payouts**: Payouts marked `failed` are not automatically retried. A production system should define a retry policy and surface failed payouts to an admin interface.
- **Single currency**: The schema stores amounts as bare integers with no currency column. Multi-currency support would require adding a `currency` field and handling conversion.
- **No soft deletes on financial records**: `Payout` and `LedgerEntry` records have no `deleted_at` column. Deleting any of these rows (e.g. via cascading foreign keys) would permanently destroy audit history.
