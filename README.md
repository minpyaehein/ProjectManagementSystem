# Project Management System

A Laravel-based project management platform for tracking projects, customers, suppliers, users, roles, and financial performance.

## Overview

This application is designed to centralize core business operations for project-driven teams. It includes modules for:

- Project management and status tracking
- Customer and supplier records
- User and role management with permission-based access
- Project financial tracking for costs and revenue
- Authentication and user profile management
- Activity logging and auditing

## Tech Stack

- PHP 8.2
- Laravel 12
- Blade templates
- Vite
- Tailwind CSS
- MySQL / SQLite-compatible database setup via Laravel

## Key Features

### Project Management
- Create and manage project records
- Track project lifecycle and activation status
- Associate project costs and revenue entries
- Export project data when needed

### Customer and Supplier Management
- Maintain customer and supplier profiles
- Manage contacts and business relationships
- Deactivate or reactivate records as needed

### User & Role Administration
- User registration and authentication
- Role-based authorization with permission checks
- Secure password reset and profile updates
- Activity tracking for administrative actions

### Financial Tracking
- Record project-related cost entries
- Record project-related revenue entries
- Manage financial details directly from the project workflow

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   └── Middleware/
├── Models/
bootstrap/
config/
Database/
public/
resources/
routes/
storage/
tests/
```

Main application routes are defined in `routes/web.php`, and the main business logic is organized across the controllers and models under `app/`.

## Requirements

Before running the project, make sure you have:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- A database such as MySQL, PostgreSQL, or SQLite

## Installation

1. Clone the repository:

```bash
git clone https://github.com/minpyaehein/ProjectManagementSystem.git
cd ProjectManagementSystem
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
```

4. Copy the environment file:

```bash
cp .env.example .env
```

5. Generate the application key:

```bash
php artisan key:generate
```

6. Configure your database in `.env`.

7. Run database migrations:

```bash
php artisan migrate
```

8. Start the app:

```bash
php artisan serve
```

9. In a separate terminal, run the Vite dev server:

```bash
npm run dev
```

## Production Build

```bash
npm run build
```

## Common Commands

```bash
php artisan test
php artisan route:list
php artisan migrate:fresh --seed
```

## License

This project is open source and available under the [MIT License](LICENSE).

## Notes

This repository is built on the Laravel framework and follows a standard MVC structure. It is suitable for internal business process management and can be extended to add additional modules such as invoices, reporting, dashboards, or document tracking.
