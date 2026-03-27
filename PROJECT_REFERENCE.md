# Project Reference

This file is the single high-level reference for the current `shop-backend` project state.

Use this file in future prompts as the project context anchor.

## Stack

- Laravel 13
- PHP 8.3
- SQLite for current local development
- Blade for web storefront and admin panel
- JSON API for mobile app/backend integrations
- Custom database-backed bearer token auth for API clients
- Session auth for web/admin screens

## Current Product Scope

The project now includes three surfaces in one Laravel app:

1. Public web storefront
2. Admin web panel
3. Mobile-ready API

Current business theme:

- Fast food / quick service restaurant
- Pizza, burgers, sides, shakes, and similar menu items
- Customer ordering flow is menu-based, not inventory-heavy retail

## Main Areas

- Web routes: [routes/web.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/routes/web.php)
- API routes: [routes/api.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/routes/api.php)
- API controllers: [app/Http/Controllers/Api](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api)
- Web controllers: [app/Http/Controllers/Web](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web)
- Models: [app/Models](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models)
- Shared checkout logic: [app/Services/CheckoutService.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Services/CheckoutService.php)
- Blade views: [resources/views](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views)
- Seed data: [database/seeders/DatabaseSeeder.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/database/seeders/DatabaseSeeder.php)
- Tests: [tests/Feature](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/tests/Feature)

## Data Model

### Users

File: [app/Models/User.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/User.php)

Important fields:

- `name`
- `email`
- `password`
- `is_admin`
- `phone`
- `address_line`
- `city`
- `area`
- `postal_code`

Relations:

- `apiTokens()`
- `cartItems()`
- `orders()`

Notes:

- Web login uses Laravel session auth.
- API login uses custom bearer tokens stored in `api_tokens`.
- Customer profile data is also used to prefill checkout.

### App Settings

Files:

- [app/Models/AppSetting.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/AppSetting.php)
- [app/Http/Controllers/Web/Admin/SettingController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web/Admin/SettingController.php)

Purpose:

- Site/company name
- Site tagline
- Contact phone/email/address
- Logo path

These settings are injected into all Blade views through [app/Providers/AppServiceProvider.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Providers/AppServiceProvider.php).

### Categories

File: [app/Models/Category.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/Category.php)

Important fields:

- `name`
- `slug`
- `description`
- `image_url`
- `is_active`
- `sort_order`

Relation:

- `products()`

### Products

File: [app/Models/Product.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/Product.php)

Important fields:

- `category_id`
- `name`
- `slug`
- `sku`
- `short_description`
- `description`
- `price`
- `sale_price`
- `currency`
- `stock`
- `image_url`
- `is_active`
- `is_featured`

Relation:

- `category()`

Helper:

- `currentPrice()` returns `sale_price` when present, otherwise `price`

Important domain note:

- The legacy `stock` column still exists in the database schema for compatibility
- The app no longer uses stock-driven ordering logic for customer checkout
- Menu availability is currently controlled by `is_active`

### API Tokens

File: [app/Models/ApiToken.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/ApiToken.php)

Purpose:

- Mobile/API bearer token storage

Important fields:

- `user_id`
- `name`
- `token` hashed with SHA-256
- `last_used_at`
- `expires_at`

### Cart Items

File: [app/Models/CartItem.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/CartItem.php)

Important fields:

- `user_id`
- `product_id`
- `quantity`
- `unit_price`
- `currency`

Notes:

- One row per user/product combination
- Unique index on `user_id + product_id`

### Orders

Files:

- [app/Models/Order.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/Order.php)
- [app/Models/OrderItem.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Models/OrderItem.php)

Order fields:

- `user_id`
- `number`
- `status`
- `subtotal`
- `currency`
- `customer_name`
- `customer_email`
- `customer_phone`
- `shipping_address`
- `notes`

Order item fields:

- `order_id`
- `product_id`
- `product_name`
- `product_sku`
- `quantity`
- `unit_price`
- `line_total`
- `currency`

Order statuses currently used:

- `placed`
- `processing`
- `completed`
- `cancelled`

## Authentication Model

## Web Auth

File: [app/Http/Controllers/Web/AuthController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web/AuthController.php)

Routes:

- `GET /login`
- `POST /login`
- `GET /register`
- `POST /register`
- `POST /logout`

Behavior:

- Standard Laravel session login
- After login:
  - admin users go to `/admin`
  - normal users go to `/`

## API Auth

Files:

- [app/Http/Controllers/Api/AuthController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/AuthController.php)
- [app/Http/Middleware/AuthenticateApiToken.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Middleware/AuthenticateApiToken.php)

Routes:

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/logout`

Behavior:

- Token returned as plain bearer token once
- DB stores only hashed token
- Middleware resolves user by hashed bearer token

## Authorization

Files:

- [app/Http/Middleware/EnsureUserIsAdmin.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Middleware/EnsureUserIsAdmin.php)
- [bootstrap/app.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/bootstrap/app.php)

Middleware aliases:

- `api.token`
- `admin`

Meaning:

- `api.token` protects API routes with bearer token auth
- `admin` checks `user()->is_admin`

Important:

- The `admin` middleware is used for both API admin routes and web admin routes
- Web admin routes are also inside `auth`

## Public Web Storefront

Main controller:

- [app/Http/Controllers/Web/StorefrontController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web/StorefrontController.php)

Main views:

- [resources/views/layouts/store.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/layouts/store.blade.php)
- [resources/views/store/home.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/store/home.blade.php)
- [resources/views/store/category.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/store/category.blade.php)
- [resources/views/store/product.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/store/product.blade.php)
- [resources/views/store/cart.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/store/cart.blade.php)
- [resources/views/store/checkout.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/store/checkout.blade.php)
- [resources/views/store/orders/index.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/store/orders/index.blade.php)
- [resources/views/store/orders/show.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/store/orders/show.blade.php)

Web storefront routes:

- `GET /`
- `GET /categories/{category:slug}`
- `GET /products/{product:slug}`
- `GET /cart`
- `POST /cart`
- `PATCH /cart/{cartItem}`
- `DELETE /cart/{cartItem}`
- `GET /checkout`
- `POST /checkout`
- `GET /orders`
- `GET /orders/{order}`
- `GET /profile`
- `PUT /profile`

Important behavior:

- Cart/checkout/orders require logged-in web user
- Product detail page lets logged-in user add to cart
- Listing cards also allow direct add-to-cart
- Checkout uses shared checkout service
- Storefront is styled as a fast-food ordering UI
- Users can store phone/address details in their profile
- Checkout pre-fills address/contact data from the profile and saves updates back to the user

## Admin Panel

Layout:

- [resources/views/layouts/admin.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/layouts/admin.blade.php)

Controllers:

- [app/Http/Controllers/Web/Admin/DashboardController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web/Admin/DashboardController.php)
- [app/Http/Controllers/Web/Admin/CategoryController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web/Admin/CategoryController.php)
- [app/Http/Controllers/Web/Admin/ProductController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web/Admin/ProductController.php)
- [app/Http/Controllers/Web/Admin/OrderController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Web/Admin/OrderController.php)

Views:

- [resources/views/admin/dashboard.blade.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/admin/dashboard.blade.php)
- [resources/views/admin/categories](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/admin/categories)
- [resources/views/admin/products](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/admin/products)
- [resources/views/admin/orders](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/resources/views/admin/orders)

Admin web routes:

- `GET /admin`
- `resource /admin/categories` except `show`
- `resource /admin/products` except `show`
- `GET /admin/orders`
- `GET /admin/orders/{order}`
- `PATCH /admin/orders/{order}`
- `GET /admin/settings`
- `POST /admin/settings`

Admin panel features:

- Dashboard stats
- Recent orders
- Featured menu summary
- Category CRUD
- Product CRUD
- Order detail view
- Order status update
- Brand/company settings management
- Logo upload

## API Surface

All API routes are under `/api/v1`.

### Public API

- `GET /categories`
- `GET /categories/{category}`
- `GET /products`
- `GET /products/{product}`

### Auth API

- `POST /auth/register`
- `POST /auth/login`
- `GET /auth/me`
- `POST /auth/logout`

### Customer API

- `GET /cart`
- `POST /cart/items`
- `PATCH /cart/items/{cartItem}`
- `DELETE /cart/items/{cartItem}`
- `GET /orders`
- `GET /orders/{order}`
- `POST /checkout`

### Admin API

- `apiResource /admin/categories`
- `apiResource /admin/products`

API controllers:

- [app/Http/Controllers/Api/CategoryController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/CategoryController.php)
- [app/Http/Controllers/Api/ProductController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/ProductController.php)
- [app/Http/Controllers/Api/AuthController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/AuthController.php)
- [app/Http/Controllers/Api/CartController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/CartController.php)
- [app/Http/Controllers/Api/OrderController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/OrderController.php)
- [app/Http/Controllers/Api/CheckoutController.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/CheckoutController.php)
- [app/Http/Controllers/Api/Admin](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Http/Controllers/Api/Admin)

## Shared Checkout Flow

File:

- [app/Services/CheckoutService.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/app/Services/CheckoutService.php)

This service is the core order placement logic used by:

- API checkout
- Web checkout

Behavior:

1. Lock cart rows
2. Validate cart is not empty
3. Lock products
4. Validate active status
5. Create order
6. Create order items
7. Clear cart

If checkout logic changes in the future, update this service first.

## Database / Migrations

Migration folder:

- [database/migrations](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/database/migrations)

Custom ecommerce migrations added:

- `2026_03_27_172940_create_categories_table.php`
- `2026_03_27_172940_create_products_table.php`
- `2026_03_27_180000_add_is_admin_to_users_table.php`
- `2026_03_27_180100_create_api_tokens_table.php`
- `2026_03_27_180200_create_cart_items_table.php`
- `2026_03_27_180300_create_orders_table.php`
- `2026_03_27_180400_create_order_items_table.php`

## Seed Data

File:

- [database/seeders/DatabaseSeeder.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/database/seeders/DatabaseSeeder.php)

Seeded users:

- Admin: `admin@example.com` / `password`
- Customer: `test@example.com` / `password`
- Customer: `customer@example.com` / `password`

Seeded catalog:

- Categories: Pizza, Burgers, Sides & Drinks
- Demo menu items for fast-food ordering
- Default brand settings for RushBite are also seeded

Important:

- Seeder is idempotent now
- Re-running seed should not fail on duplicate users

## Current Tests

Feature tests:

- [tests/Feature/CatalogApiTest.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/tests/Feature/CatalogApiTest.php)
- [tests/Feature/AuthApiTest.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/tests/Feature/AuthApiTest.php)
- [tests/Feature/AdminCatalogApiTest.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/tests/Feature/AdminCatalogApiTest.php)
- [tests/Feature/CheckoutApiTest.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/tests/Feature/CheckoutApiTest.php)
- [tests/Feature/WebAppTest.php](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/tests/Feature/WebAppTest.php)

What is covered:

- Catalog API
- Mobile auth API
- Admin API authorization and CRUD
- Checkout API
- Storefront homepage render
- Admin dashboard access
- Web cart flow
- Web profile update flow
- Admin brand settings update flow

## Run / Test Commands

### First setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### Run app

```bash
php artisan serve
```

### Useful URLs

- Storefront: `http://127.0.0.1:8000/`
- Login: `http://127.0.0.1:8000/login`
- Register: `http://127.0.0.1:8000/register`
- Admin: `http://127.0.0.1:8000/admin`
- API base: `http://127.0.0.1:8000/api/v1`
- Health check: `http://127.0.0.1:8000/up`

### Verify project

```bash
./vendor/bin/pint --test
php artisan test
php artisan route:list
```

## Important Implementation Notes

- The mobile app client UI does not exist yet in this repo.
- The API is ready for a future Flutter or React Native app.
- Web/admin and API share the same models and database.
- Order placement logic is centralized in `CheckoutService`.
- API auth is custom token-based, not Sanctum-based.
- Web uses Laravel session auth.
- Admin authorization depends on `users.is_admin`.
- Customer ordering does not currently use stock-based validation.
- Uploaded logos/category images/product images are served through `public/storage`
- Company branding comes from `app_settings`, not hardcoded view strings anymore

## Database Management

Current local setup:

- SQLite database file at [database/database.sqlite](/Applications/XAMPP/xamppfiles/htdocs/shop-backend/database/database.sqlite)

Common commands:

```bash
php artisan migrate
php artisan db:seed
php artisan migrate:rollback
php artisan migrate:fresh --seed
```

Recommended production database:

- MySQL or MariaDB

Production setup path:

1. Create production database
2. Update `.env` database variables
3. Run `php artisan migrate --force`
4. Run `php artisan db:seed --force` only if demo/default data is desired

Important:

- Do not rely on SQLite for production if multiple users/orders are expected
- Use backups for the production database
- Uploaded files in `storage/app/public` should also be backed up

## Deployment Notes

Recommended production deployment target:

- Shared hosting with PHP 8.3+ and MySQL, or
- VPS with Nginx/Apache, PHP-FPM, MySQL/MariaDB

Basic deployment steps:

1. Copy project to server
2. Run `composer install --no-dev --optimize-autoloader`
3. Create `.env`
4. Set `APP_ENV=production`
5. Set `APP_DEBUG=false`
6. Configure MySQL/MariaDB credentials
7. Run `php artisan key:generate`
8. Run `php artisan migrate --force`
9. Run `php artisan storage:link`
10. Set correct write permissions for `storage` and `bootstrap/cache`
11. Run:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Server requirements:

- Web root should point to `public/`
- `storage/` and `bootstrap/cache/` must be writable
- Uploaded files must persist across deployments

If deploying with XAMPP locally:

- Put project in htdocs
- Serve via Apache pointing to the Laravel `public` folder if possible
- Or continue using `php artisan serve` for local dev only

## Company Branding

Editable from admin:

- Company/brand name
- Logo
- Tagline
- Contact phone
- Contact email
- Contact address

Admin location:

- `/admin/settings`

Customer-facing result:

- Header brand name/logo
- Footer contact details

## Recommended Change Rules

When editing this project later:

1. Read this file first.
2. If checkout behavior is involved, inspect `CheckoutService`.
3. If auth behavior is involved, distinguish web session auth from API bearer token auth.
4. If admin access changes, inspect both `EnsureUserIsAdmin` and `routes/web.php` and `routes/api.php`.
5. If product/category behavior changes, check both web controllers and API controllers.
6. Keep tests updated for both API and web flows.

## If We Add More Later

Likely next modules:

- Payment gateway integration
- Address book / multiple addresses
- Shipping fees and tax logic
- Product image upload instead of URL-only images
- Search/filter UI improvements
- Native mobile app client
- Admin user management

When any of these are added, update this file in the same change.
