# Copilot Instructions for Nenshi Mobile

This document provides essential context for AI agents working in this codebase.

## Project Overview

Nenshi Mobile is a Laravel + Vue.js application for managing industrial operations, with features for:
- Machine operation tracking
- Employee management
- Plant/factory management
- Task assignment and monitoring
- Excel report generation

## Architecture

### Backend (Laravel)
- Models (`app/Models/`):
  - `Employee`, `Plant`, `MachineType`, `MachineNumber`, `Task`, `MainOperation`
  - Uses Laravel's Eloquent ORM with relationships
- Controllers (`app/Http/Controllers/`):
  - RESTful controllers following Laravel conventions
  - Authentication handled by `AuthenticateController`
  - Main business logic in `MainOperationController`

### Frontend (Vue.js)
- Uses Inertia.js for seamless SPA experience
- Components in `resources/js/Components/`
- Pages in `resources/js/Pages/`
- Form handling with `useForm` from `@inertiajs/vue3`

## Key Patterns

1. Resource Management:
```php
// Example from MachineNumberController.php
public function index() {
    $machineNumbers = MachineNumberResource::collection(
        MachineNumber::with('machineTypePlant.machineType','machineTypePlant.plant')->get()
    );
    return inertia('MachineNumbers/Index', ['machineNumbers'=> $machineNumbers]);
}
```

2. Vue Component Props:
```vue
// Pattern for component props
const props = defineProps({
  mainoperations: { type: Object, required: true },
  machinetypes: { type: Object, required: true },
  // ...other props
});
```

## Development Workflow

1. Setup:
```bash
composer install
npm install
cp .env.example .env
touch database/database.sqlite
php artisan migrate
php artisan key:generate
```

2. Development:
```bash
php artisan serve    # Run Laravel server
npm run dev         # Run Vite for frontend
```

## Important Conventions

1. Authentication:
   - Uses custom Employee model instead of default User model
   - Admin routes protected by 'admin' middleware

2. Route Organization:
   - Auth routes in `routes/auth.php`
   - Admin routes grouped under admin middleware

3. Data Export:
   - Excel exports handled by Maatwebsite/Laravel-Excel
   - Export configurations in `app/Exports/`

## Japanese Language Support
- UI text is in Japanese
- Error messages and success notifications use Japanese text
- Follow existing patterns for consistency

## Common Tasks

1. Adding new machine operations:
   - Create migration
   - Add Model with relationships
   - Create Controller with Resource methods
   - Add Vue components for UI
   - Update routes in `auth.php`

2. Excel Export:
   - Use `MainOperationsExport` as reference
   - Implement through controller methods
   - Use blob response type for downloads