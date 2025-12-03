## E-commerce Backend (Laravel)

This project is a small **e-commerce backend** built with Laravel. It exposes a REST API for:

- **Authentication** (Sanctum tokens)
- **Product management** (CRUD, admin-only for writes)
- **Stock tracking** (deduct on checkout, admin restock)
- **Shopping cart** (add/update/remove/view with subtotal)
- **Checkout & orders** (convert cart to order, persist items)
- **Payment simulation** (mark order as paid)

It is designed as a backend-only service, to be consumed by Postman or a separate frontend.

---

## Tech Stack

- **Framework**: Laravel (PHP 8.2)
- **Auth**: Laravel Sanctum (personal access tokens)
- **Database**: SQLite (default), via Eloquent ORM
- **Storage**: Local/public disk for product images

---

## Features vs Requirements

1. **Authentication**
   - User registration: `POST /api/register`
   - User login: `POST /api/login`
   - Token-based auth: Sanctum, Bearer tokens in `Authorization` header
   - Logout: `POST /api/logout` (revokes current token)

2. **Product Management (CRUD)**
   - Public list: `GET /api/products`
   - Public detail: `GET /api/products/{product}`
   - Admin-only create: `POST /api/admin/products`
   - Admin-only update: `PUT /api/admin/products/{product}`
   - Admin-only delete: `DELETE /api/admin/products/{product}`
   - Fields: `name`, `description`, `price`, `stock`, `image_url`
   - Optional image upload stored on the `public` disk

3. **Stock Management**
   - Stock is **deducted only during checkout** (`CheckoutController`)
   - Checkout fails if requested quantity exceeds available stock
   - Admin restock endpoint: `POST /api/admin/restock`

4. **Cart System**
   - Add item: `POST /api/cart/add`
   - Update quantity: `PUT /api/cart/update`
   - Remove item: `DELETE /api/cart/remove`
   - View cart + subtotal: `GET /api/cart`

5. **Checkout Flow**
   - `POST /api/checkout` converts the authenticated user's cart into an order
   - Validates stock for each cart item
   - Deducts stock on the associated products
   - Clears cart items after successful order creation
   - All within a database transaction

6. **Orders & Order Items**
   - Each order has many items (`Order` / `OrderItem` models)
   - User history: `GET /api/orders/my`
   - User can view own order: `GET /api/orders/{order}`
   - Admin can view all orders: `GET /api/admin/orders`

7. **Payment Simulation**
   - `POST /api/payment/simulate`
   - Always returns `{"payment_successful": true, "order": ...}`
   - Sets `order.status` to `paid`

---

## Database Design

Main tables (see `database/migrations` for details):

- `users`: standard Laravel users + `is_admin` boolean
- `products`: `name`, `description`, `price`, `stock`, `image_url`
- `carts`: `user_id`
- `cart_items`: `cart_id`, `product_id`, `quantity`, `unit_price`
- `orders`: `user_id`, `total`, `status`
- `order_items`: `order_id`, `product_id`, `quantity`, `unit_price`
- `personal_access_tokens`: Sanctum tokens

Relationships are defined via Eloquent models in `app/Models`.

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer

### Install dependencies

```bash
composer install
```

### Environment configuration

Copy the example env file and generate an app key:

```bash
cp .env.example .env
php artisan key:generate
```

By default this project uses **SQLite** with `DB_CONNECTION=sqlite`.

Ensure the SQLite database file exists (if it does not, create an empty file):

```bash
touch database/database.sqlite
```

### Run migrations and seeders

```bash
php artisan migrate --seed
```

This will create:

- An **admin** user:
  - Email: `admin@example.com`
  - Password: `password`
- A **customer** user:
  - Email: `customer@example.com`
  - Password: `password`
- A couple of sample `products`.

### Run the development server

```bash
php artisan serve
```

The app will be available at:

- `http://127.0.0.1:8000`

Make sure your Postman `base_url` matches this.

---

## Authentication & Tokens

Authentication uses **Laravel Sanctum** personal access tokens.

Typical flow:

1. `POST /api/register` or `POST /api/login` with email/password.
2. The response body includes a `token` string.
3. For subsequent requests, add header:

   ```http
   Authorization: Bearer <token>
   ```

Protected routes (cart, checkout, orders, admin) all require this header.

---

## Postman Collection

A ready-to-import Postman collection is included in the project root:

- `ecommerce-backend.postman_collection.json`

### Import

1. Open Postman.
2. Click **Import** and select `ecommerce-backend.postman_collection.json`.
3. Create a Postman **Environment**, e.g. `Ecommerce Local`, with variables:
   - `base_url = http://127.0.0.1:8000`
   - `token =` (leave blank initially)

### Usage

- Use **Auth → Login** request:
  - On success, its test script sets `{{token}}` in the environment automatically.
- Then call other folders:
  - **Products** (public and admin CRUD)
  - **Cart** (view/add/update/remove)
  - **Checkout & Payment** (checkout, simulate payment)
  - **Orders (User)** and **Orders (Admin)**
  - **Admin** (restock)

---

## API Endpoint Overview

### Auth

- `POST /api/register` – Register new user, returns `user` + `token`.
- `POST /api/login` – Login existing user, returns `user` + `token`.
- `POST /api/logout` – Revoke current access token.

### Products

- `GET /api/products` – List products (public).
- `GET /api/products/{product}` – Show product details (public).
- `POST /api/admin/products` – Create product (admin only).
- `PUT /api/admin/products/{product}` – Update product (admin only).
- `DELETE /api/admin/products/{product}` – Delete product (admin only).

### Cart (auth required)

- `GET /api/cart` – View cart items + subtotal for authenticated user.
- `POST /api/cart/add` – Add product to cart (or increment quantity).
- `PUT /api/cart/update` – Update quantity; quantity `0` removes item.
- `DELETE /api/cart/remove` – Remove a product from the cart.

### Checkout & Payment (auth required)

- `POST /api/checkout` – Convert cart to order, validate stock, clear cart.
- `POST /api/payment/simulate` – Mark an order as paid, always returns `payment_successful: true`.

### Orders (User)

- `GET /api/orders/my` – List orders belonging to the authenticated user.
- `GET /api/orders/{order}` – Show a single order (if owner or admin).

### Orders (Admin)

- `GET /api/admin/orders` – List all orders with user and items (admin only).

### Admin

- `POST /api/admin/restock` – Increase stock of a product (admin only).

---

## Running Tests

You can run the default test suite with:

```bash
php artisan test
```

Feature and unit tests live under `tests/Feature` and `tests/Unit`.

---

## License

This project is based on the Laravel framework, which is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

