# Warehouse Management System (Laravel API)

A RESTful backend for managing warehouse inventory — suppliers, products, purchase orders, stock receiving, and stock export/dispatch — built with Laravel 13 and PHP 8.3 as a hands-on architecture learning project.

The focus of this project was practicing clean backend architecture: separating HTTP handling, business logic, and data access into distinct layers, and correctly handling concurrency and partial-fulfillment scenarios that come up in real inventory systems.

## Features

- **Authentication** — token-based auth via Laravel Sanctum (register/login), with all resource routes protected behind `auth:sanctum`
- **Core inventory resources** — full CRUD for Warehouses, Categories, Suppliers, and Products
- **Purchase orders** — create a purchase order with line items, then receive stock against it (partial receiving supported per line item)
- **Stock exports** — create an export order with line items, then dispatch stock against it (partial dispatching supported; status automatically moves through `pending → partly_dispatched → dispatched`)
- **Concurrency-safe stock updates** — increment/decrement operations use row-level locking (`lockForUpdate`) inside DB transactions to prevent race conditions when multiple requests touch the same stock record
- **Insufficient-stock protection** — dispatch attempts are validated against remaining quantity and available stock, returning a `409 Conflict` with a clear error message instead of allowing overselling

## Architecture

The app follows a layered structure rather than putting logic in controllers:

```
Controller  ->  Service  ->  Repository  ->  Model
              (business      (data
               logic)         access)
```

- **Controllers** (`app/Http/Controllers/Api`) — validate requests and return responses only
- **Services** (`app/Services`) — orchestrate business logic and DB transactions (e.g. `StockExportService::dispatch()`, `PurchaseOrderService::createWithItems()`)
- **Repositories** (`app/Repositories`) — encapsulate Eloquent queries behind a `BaseRepository`, including the locking logic in `StockRepository`
- **DTOs** (`app/DTOs`) — typed, readonly data objects (e.g. `ReceiveStockDTO`, `DispatchStockDTO`) used to pass validated request data into services instead of raw arrays

## Data model

`Warehouse`, `Supplier`, `Category`, `Product`, `Stock`, `PurchaseOrder` + `PurchaseOrderItem`, `StockExport` + `StockExportItem` — with stock tracked per warehouse/product pair, and purchase/export items tracking `quantity` vs. `quantity_received` / `quantity_dispatched` to support partial fulfillment.

## Tech stack

- PHP 8.3, Laravel 13
- SQLite (development)
- Laravel Sanctum (API authentication)

## API overview

All endpoints below (except register/login) require a Sanctum bearer token.

| Resource | Endpoints |
|---|---|
| Auth | `POST /register`, `POST /login` |
| Warehouses | full REST resource (`GET/POST/PUT/DELETE /warehouse`) |
| Categories, Suppliers, Products | `GET`, `POST /create`, `GET /{id}`, `PUT /update/{id}`, `DELETE /delete/{id}` |
| Purchase Orders | CRUD + `POST /purchaseOrder/{id}/receive` |
| Stock Exports | CRUD + `POST /stockExport/{id}/dispatch` |

See [`API_Test.http`](./API_Test.http) for ready-to-run example requests.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

## Status

This is an actively developed learning project — routes and validation are functional, automated test coverage is still a work in progress.