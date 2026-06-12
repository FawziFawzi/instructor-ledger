# AI Usage

This document explains how AI tooling was used during the development of Instructor Ledger.

---

## What was built with AI assistance

### Test file structure and Pest syntax

The Pest feature test file (`tests/Feature/PayoutSystemTest.php`) was initially written with a structural error — Pest's top-level `it()` functions were placed inside a PHPUnit class body, which is invalid. Claude Code identified the root cause, rewrote the file as a proper Pest test (removing the class wrapper, adding `uses()`, adding all missing `use` imports, and fixing a missing closing `});`), and corrected the class name reference from `MockPaymentGateway` to `MockPaymentGatewayService`.

### Documentation

The documentation files (`README.md`, `docs/ARCHITECTURE.md`, `docs/AI_USAGE.md`) were written with AI assistance after exploring the codebase — reading models, services, jobs, commands, migrations, routes, and configuration — to produce accurate, project-specific documentation rather than generic boilerplate.

### Planning and architecture discussions

ChatGPT was heavily used during the planning and iteration process of the system architecture and payout flow. AI assistance was used for:

* discussing database structure ideas,
* payout flow planning,
* queue and reconciliation flow planning,
* testing strategy brainstorming,
* and refining the financial pipeline architecture.

### Mistakes and refinements
* redesigning the database schema to better match the subscription revenue-sharing domain,
* simplifying unnecessary table structures,
* correcting enum usage and status modeling,
* improving the instructor balance architecture,
* refining the pending → available → payout lifecycle,
* redesigning instructor percentage allocation handling,
* improving retry/reconciliation thinking,
* and adjusting service boundaries and responsibilities.

The final architecture and implementation decisions reflect manual engineering decisions and iterative refinement rather than direct AI-generated implementation.

---

## How AI was used

* **Code exploration**: Claude Code read source files across the project to understand the domain model, service interactions, and payout flow before writing documentation.
* **Structural diagnosis**: The Pest test error was diagnosed by reading the file and identifying the class/function nesting problem, not by guessing or applying a generic fix.
* **Planning assistance**: ChatGPT was used interactively to discuss architectural approaches, tradeoffs, testing ideas, retry flows, reconciliation handling, and payout safety strategies.
* **Iterative refinement**: AI-generated ideas were reviewed critically and frequently modified, simplified, or redesigned during implementation.
* **Content authoring**: Documentation was authored based on observed code behaviour, not assumptions. Every architectural description corresponds to logic implemented in the repository.

---

## What AI did not do

* AI did not independently build the final financial architecture.
* AI did not independently design the final database schema.
* AI did not independently define the final payout lifecycle or balance states.
* AI did not independently make production architectural decisions.
* AI suggestions were reviewed, corrected, simplified, or redesigned before implementation.
* The final domain behavior, reconciliation logic, balance movement lifecycle, and payout safety rules were manually decided during development.

---

## Model used

* **Claude Sonnet 4.6** via Claude Code CLI (Anthropic, 2026)
* **ChatGPT GPT-5.5** (OpenAI, 2026)

---

## Prompts that produced this output

The documentation and planning discussions were generated through iterative conversations focused on:

* payout architecture,
* revenue allocation,
* queue processing,
* idempotency,
* reconciliation,
* retry handling,
* database design,
* testing,
* and financial consistency guarantees.

Documentation generation also included the following instruction:

> Make factories and seeders
> 
> Make Documentation
>
> * README.md — Setup instructions, How to run tests, Assumptions made
> * /docs/ARCHITECTURE.md — Key architectural decisions, Revenue allocation strategy, Idempotency approach, Provider timeout handling, Scaling considerations, Known limitations
> * /docs/AI_USAGE.md — Include the AI explanation described above.
