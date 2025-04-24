# CCS Dashboard – Mumbai Metro Line One

## Overview

This project is the **backbone dashboard system for CCS (Centralised Computing System)** used in **Mumbai Metro Line One**. It manages and configures all AFC (Automatic Fare Collection) systems including:

- **TVM** – Ticket Vending Machines
- **TOM** – Ticket Operating Machines
- **SCS** – Station Computing Systems
- **AG** – Automatic Gates

Through this centralized dashboard:

- All **fare structures**, **pass data**, and **station configurations** are managed.
- APIs are provided to **sync configurations** across all connected AFC equipment.
- All systems receive their configurations directly from this platform.
- It also facilitates **data reconciliation** for the metro using the CCS database.

## Tech Stack

- **Frontend**: Vue.js (with Webpack)
- **Backend**: Laravel (PHP Framework)
- **Database**: PostgreSQL (as per environment setup)
- **Architecture**: RESTful APIs with centralized configuration distribution

## Features

- Dynamic fare and pass configuration
- Real-time data sync APIs
- Equipment-wise configuration control
- CCS-based transaction reconciliation system
- Role-based dashboard access

## Git Commit Conventions

To maintain a clean and readable Git history, follow **Conventional Commit** guidelines:

| Prefix     | Purpose                                                   |
|------------|-----------------------------------------------------------|
| `feat`     | A new feature (e.g., adding a new API or module)          |
| `fix`      | A bug fix                                                 |
| `refactor` | Code refactoring (non-breaking, no new feature or fix)    |
| `chore`    | Routine tasks (e.g., dependencies, build scripts)         |
| `docs`     | Changes to documentation                                  |
| `test`     | Adding or modifying tests                                 |
| `style`    | Code style updates (e.g., formatting, indentation)        |

### Example Commit Messages

```bash
git commit -m "feat: Add API for station fare sync"
git commit -m "fix: Resolve TOM configuration loading issue"
git commit -m "refactor: Improve readability of fare config service"
