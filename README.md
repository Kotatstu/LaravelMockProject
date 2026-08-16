# Warehouse Management System

A full-stack warehouse management application — suppliers, products, purchase orders, stock receiving, and stock export/dispatch — built as a hands-on learning project covering clean backend architecture Laravel and Vue 3.

The focus throughout was practicing real-world patterns: layered backend architecture, concurrency-safe stock operations, partial-fulfillment workflows, and a token-authenticated SPA consuming a REST API.

## Project structure

This is a monorepo with two independent applications:

```
├── LaravelMockProject/  
└── warehouse_frontend/    
```

## Features

- **Authentication** — token-based auth via Laravel Sanctum (register/login), all resource routes protected behind `auth:sanctum`, with a route guard and persistent session on the frontend
- **Core inventory resources** — full CRUD for Warehouses, Categories, Suppliers, and Products, with relational dropdowns (Product → Category/Supplier)
- **Purchase orders** — create an order with multiple line items, then receive stock against it. Partial receiving is supported per line item, with status automatically progressing `pending → partly_received → received`
- **Stock exports** — create an export order with line items, then dispatch stock against it. Partial dispatching is supported the same way, with status progressing `pending → partly_dispatched → dispatched`
- **Concurrency-safe stock updates** — increment/decrement operations use row-level locking (`lockForUpdate`) inside DB transactions, preventing race conditions when multiple requests touch the same stock record
- **Insufficient-stock protection** — dispatch attempts are validated against remaining quantity and available stock, returning a `409 Conflict` with a clear message instead of allowing overselling
- **Referential integrity guards** — deleting a Category, Supplier, Warehouse, or Product that's still referenced elsewhere is blocked with a `409` rather than a raw database error

## Backend architecture

The API follows a layered structure rather than putting logic in controllers:

```
Controller  ->  DTO  ->  Service  ->  Repository  ->  Model
 (HTTP only)   (typed    (business    (data
                data)     logic)       access)
```

- **Controllers** (`app/Http/Controllers/Api`) — validate requests and return responses only
- **DTOs** (`app/DTOs`) — typed, readonly objects (e.g. `ReceiveStockDTO`, `DispatchStockDTO`) built from validated data, decoupling services from raw request arrays
- **Services** (`app/Services`) — orchestrate business logic and DB transactions (e.g. `StockExportService::dispatch()`, `PurchaseOrderService::createWithItems()`)
- **Repositories** (`app/Repositories`) — encapsulate Eloquent queries behind a shared `BaseRepository`, including the row-locking logic in `StockRepository`

### Data model

`Warehouse`, `Supplier`, `Category`, `Product`, `Stock`, `PurchaseOrder` + `PurchaseOrderItem`, `StockExport` + `StockExportItem` — stock is tracked per warehouse/product pair, and order line items track `quantity` against `quantity_received` / `quantity_dispatched` to support partial fulfillment.

## Frontend architecture

A Vue 3 SPA (Composition API + `<script setup>`) consuming the Laravel API over HTTP.

- **Pinia** — global auth state (`stores/auth.js`), persisting the Sanctum token in `localStorage` across page reloads
- **Vue Router** — nested routing under a shared `AppLayout` (sidebar navigation), with a global navigation guard redirecting unauthenticated users to `/login`
- **Axios** — a preconfigured API client (`api/axios.js`) pointed at the backend, with the bearer token attached per-request
- **Tailwind CSS v4** — utility-first styling throughout, with full dark mode support via the `dark:` variant

### Pages

| Area | Pattern |
|---|---|
| Login / Register | Standalone auth pages, public routes |
| Warehouses, Categories, Suppliers, Stocks | List page + modal for create/edit |
| Products | List page + dedicated create/edit pages (relational dropdowns) |
| Purchase Orders | List with inline nested line-items and an inline receive form per order |
| Stock Exports | List with inline nested line-items and an inline dispatch form per order |

## Tech stack

**Backend:** PHP 8.3, Laravel 13, SQLite (development), Laravel Sanctum
**Frontend:** Vue 3, Vite, Vue Router, Pinia, Axios, Tailwind CSS v4

## API overview

All endpoints below (except register/login) require a Sanctum bearer token.

| Resource | Endpoints |
|---|---|
| Auth | `POST /register`, `POST /login` |
| Warehouses | full REST resource (`GET/POST/PUT/DELETE /warehouse`) |
| Categories, Suppliers, Products, Stocks | `GET`, `POST /create`, `GET /{id}`, `PUT /update/{id}`, `DELETE /delete/{id}` |
| Purchase Orders | CRUD + `POST /purchaseOrder/{id}/receive` |
| Stock Exports | CRUD + `POST /stockExport/{id}/dispatch` |

See [`LaravelMockProject/API_Test.http`](./LaravelMockProject/API_Test.http) for ready-to-run example requests.

## Setup

### Backend

```bash
cd LaravelMockProject
composer install
cp .env.example .env
php artisan key:generate
```
Set `DB_CONNECTION=sqlite` in `.env`, then:
```bash
touch database/database.sqlite
php artisan migrate
php artisan serve
```
API runs at `http://127.0.0.1:8000`.

### Frontend

```bash
cd warehouse_frontend
npm install
npm run dev
```
App runs at `http://localhost:5173`.

## Known limitations / not yet implemented

- No automated test suite — functionality has been verified manually via REST Client and browser testing
- No API Resource classes — controllers return raw Eloquent models
- No pagination — list endpoints return full result sets
- No soft deletes — records are hard-deleted
- No loading-state indicators on the frontend during data fetches

## Status

Actively developed learning project. Core inventory workflows (receiving and exporting stock, including partial fulfillment) are fully functional end to end, across both backend and frontend.
