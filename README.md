# shopping-store

Laravel ecommerce backend API for the Shopping Store project.

This repository now uses the Laravel application on `main` as the primary codebase while preserving the older `master` history through a merge commit.

## Features

- Public catalog APIs for categories and products
- Mobile app authentication with bearer tokens
- Admin CRUD for categories and products
- Cart, orders, and checkout APIs
- Seed data and automated feature tests

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan test
```
