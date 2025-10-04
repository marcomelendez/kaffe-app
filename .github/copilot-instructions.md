# Copilot Instructions for kaffe-app

## Project Overview
- **kaffe-app** is a Laravel-based application (PHP) with a modular structure under `app/`.
- Major domains: Properties, Rooms, Plans, Bookings, and Users, each with dedicated models, controllers, and importers.
- Data importers for CSVs are in `app/Imports/`.
- Livewire is used for interactive UI components (see `app/Livewire/`).

## Key Architectural Patterns
- **Domain separation:** Each business domain (e.g., Property, Room, Plan) has its own model, controller, and often import logic.
- **Resourceful controllers** in `app/Http/Controllers/` follow Laravel conventions, but some API endpoints may have custom logic.
- **Livewire components** are organized by feature in `app/Livewire/`.
- **Traits** in `app/Traits/` are used for code reuse across models and actions.
- **Service Providers** in `app/Providers/` register custom services and bindings.

## Developer Workflows
- **Run the app:** Use `php artisan serve` (Laravel default) or configure a web server to point to `public/index.php`.
- **Database:** Uses SQLite by default (`database/database.sqlite`).
- **Migrations:** `php artisan migrate`
- **Seeding:** `php artisan db:seed`
- **Testing:** `vendor/bin/pest` (preferred) or `vendor/bin/phpunit`
- **JS/CSS build:** `npm run dev` (for Vite)

## Project-Specific Conventions
- **Imports:** All CSV import logic is in `app/Imports/`, with one class per import type.
- **Translations:** Models with translations have a `Translation` model (e.g., `PropertyTranslation`).
- **API:** API endpoints are defined in `routes/api.php` and handled by controllers in `app/Http/Controllers/Api/`.
- **Admin:** Admin-specific logic is under `app/Admin/`.
- **Testing:** Pest is used for tests in `tests/Feature/` and `tests/Unit/`.

## Integration Points
- **Livewire** for dynamic UI (see `app/Livewire/`)
- **Spatie Media Library** for file uploads (see `config/media-library.php`)
- **Sanctum** for API authentication (see `config/sanctum.php`)

## Examples
- To add a new property import, create a class in `app/Imports/`, register it in the relevant controller, and update the admin UI if needed.
- To add a new API endpoint, define the route in `routes/api.php` and implement the logic in `app/Http/Controllers/Api/`.

## References
- Main config: `config/`
- Main entry: `public/index.php`
- Main app logic: `app/`
- Tests: `tests/`

---
For more details, see the README or ask for specific workflow examples.
