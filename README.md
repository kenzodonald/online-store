# Online Business System (XAMPP-ready)

This is a minimal, self-contained online business (e-commerce) system designed to run on XAMPP (Apache + PHP + MySQL).

Quick setup:

1. Start XAMPP Apache and MySQL.
2. Copy the folder `On line business system` into `C:\xampp\htdocs\online-store`.
3. Open `http://localhost/phpmyadmin` and create a new database named `online_store` (or import the provided `database.sql`).
4. Import `database.sql` into the `online_store` database.
5. Visit `http://localhost/online-store/index.php`.

Default admin credentials (demo):
- Username: `admin`
- Password: `admin123`

Files included:
- `index.php`, `product.php`, `cart.php`, `checkout.php`, `config.php`, `header.php`, `footer.php`
- `admin/` — admin pages to manage products
- `database.sql` — schema + sample data
- `assets/` — `style.css` and `script.js`

Notes:
- This is a demo scaffold. For production you must secure authentication, validate input, and harden file uploads.

User accounts and persistent carts:
- Register at `register.php` to create an account.
- Login at `login.php`. When logged in your cart will be saved in the database and restored across devices.

Database note: After importing `database.sql` run the registration form to create a user. The `database.sql` now includes `users` and `carts` tables.
 
Create a demo user (optional):
- A helper script `create_demo_user.php` is provided to insert a demo account (`demo` / `demo123`).
- To run from a browser: open `http://localhost/online-store/create_demo_user.php`.
- Or run from the command line:
```bash
php "C:/xampp/htdocs/online-store/create_demo_user.php"
```

Stripe sandbox payments:
- To enable Stripe Checkout, set your test keys in `config.php` or as environment variables `STRIPE_SECRET` and `STRIPE_PUBLISHABLE`.
- Example (Windows PowerShell):
```powershell
$env:STRIPE_SECRET = 'sk_test_...'
$env:STRIPE_PUBLISHABLE = 'pk_test_...'
```
- Use the checkout flow on the site; you'll be redirected to Stripe's test checkout. Use card number `4242 4242 4242 4242` with any valid future expiry and CVC for successful payment.
- After successful payment you'll be redirected back and the order will be created in the DB.
