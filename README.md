# Instructor Ledger

A Laravel application for managing multi-instructor revenue sharing, subscription billing, and automated payouts with idempotent payment processing and a full ledger-based audit trail.

---

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+ & npm
- MySQL / MariaDB
- Redis (required for distributed locking during payout processing)

---

## Setup Instructions

### 1. Clone and install dependencies

```bash
git clone <repo-url> instructor-ledger
cd instructor-ledger
composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
```

Edit `.env` and set your database and Redis credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=instructor_ledger
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Seed the database (optional)

```bash
php artisan db:seed
```

### 5. Build frontend assets

```bash
# Production build
npm run build

# Development with hot-reload
npm run dev
```

### 6. Start the application

```bash
php artisan serve
```

The app will be available at `http://localhost:8000`.

---

## How to Run Tests

Tests are written with [Pest](https://pestphp.com/) and require a real database connection — the suite does not mock the database layer.

```bash
# Run all tests
php artisan test

# Run only the payout system tests
php artisan test tests/Feature/PayoutSystemTest.php

```

Ensure your `.env.testing` (or the `testing` environment in `.env`) points to a separate test database to avoid destroying development data.

---

## Scheduled Commands

Two Artisan commands drive the payout pipeline and are scheduled automatically in production:

| Command | Schedule | Purpose |
|---|---|---|
| `app:process-payouts` | Monthly | Dispatch payout jobs for all instructors with a pending balance |
| `app:reconcile-payouts` | Every 30 minutes | Verify payouts stuck in `pending_verification` due to gateway timeouts |

Run them manually with:

```bash
php artisan app:process-payouts
php artisan app:reconcile-payouts
```

---

## Assumptions Made

- **Monetary values are stored as integers** (smallest currency unit, e.g. cents) to eliminate floating-point rounding errors.
- **Platform fee is fixed at 20%**, configured in `config/platform-fee.php`. It is deducted from the subscription amount before distributing revenue to instructors.
- **Instructor share percentages must sum to 100%** per subscription. `RevenueAllocationService` validates this and throws if they do not.
- **Rounding remainder goes to the last instructor** in the share list so that every cent of available revenue is allocated.
- **The mock payment gateway** (`MockPaymentGatewayService`) simulates real-world unreliability — it randomly succeeds, fails, or times out. It is not a real payment provider and is intended for development and testing only.
- **Student identity is currently hardcoded** to the first `student`-type user in `SubscriptionController::store()`. This is a temporary placeholder and would be replaced by proper authenticated user lookup in a production system.
- **Instructor shares are currently hardcoded** to the first three instructors at a 50/30/20% split in the subscription controller. A production system would derive this from actual course ownership records.
- **Redis is required** for the distributed lock in `ProcessInstructorPayoutJob`. Without it, the lock falls back to the array driver, which does not protect against race conditions across multiple processes or servers.
- **Queue workers must be running** for `ProcessInstructorPayoutJob` to execute asynchronously. In tests, the job is invoked directly via `handle()` to allow synchronous assertion.
