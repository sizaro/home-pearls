# Home Pearls Render trial deployment

Home Pearls is Laravel 12 with Livewire 4, Blade, Tailwind, Fortify, and Spatie roles. The Render trial uses a new PostgreSQL database and Cloudinary. It does not copy, migrate, alter, or delete any data or images in the previous DigitalOcean environment.

## Local setup

Install PHP 8.2+, Composer, Node 20+, and PostgreSQL. Copy `.env.example` to `.env`, configure PostgreSQL and Cloudinary, then run:

```sh
composer install
npm ci
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Before the first seed, set `INITIAL_SUPER_ADMIN_ENABLED=true` and provide the administrator's name, email, and password in the matching `INITIAL_SUPER_ADMIN_*` variables. Seed the database once, then set the flag to `false` and remove those three credentials. The interactive `php artisan app:create-initial-super-admin` command remains available if you prefer to create an administrator manually.

Use `npm run dev` in a separate terminal for frontend development.

## Required production environment

```dotenv
APP_NAME="Home Pearls"
APP_ENV=production
APP_KEY=base64:GENERATE_ONCE_AND_RETAIN_THIS_VALUE
APP_DEBUG=false
APP_URL=https://YOUR-SERVICE.onrender.com
LOG_CHANNEL=stderr
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=RENDER_POSTGRES_HOST
DB_PORT=5432
DB_DATABASE=RENDER_POSTGRES_DATABASE
DB_USERNAME=RENDER_POSTGRES_USER
DB_PASSWORD=RENDER_POSTGRES_PASSWORD
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CLOUDINARY_CLOUD_NAME=...
CLOUDINARY_API_KEY=...
CLOUDINARY_API_SECRET=...
CLOUDINARY_FOLDER=home-pearls/trial

INITIAL_SUPER_ADMIN_ENABLED=false
INITIAL_SUPER_ADMIN_NAME=
INITIAL_SUPER_ADMIN_EMAIL=
INITIAL_SUPER_ADMIN_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="Home Pearls"
```

Set `CLOUDINARY_FOLDER=home-pearls/development` locally. The different folders prevent one environment from deleting another environment’s images. Keep Cloudinary credentials only in local and Render environment settings.

## Render setup

1. Create a **new** Render PostgreSQL service. Do not connect to the old DigitalOcean database.
2. Create a Docker web service from this repository and add the variables above. Generate `APP_KEY` once using `php artisan key:generate --show` and retain it for later deploys.
3. Deploy the service. The supplied Docker image builds Vite assets, serves Laravel through Apache on Render’s `PORT`, logs to standard streams, and does not run migrations during startup.
4. For the first seed only, set these environment variables in Render with the intended administrator's details:

   ```text
   INITIAL_SUPER_ADMIN_ENABLED=true
   INITIAL_SUPER_ADMIN_NAME=...
   INITIAL_SUPER_ADMIN_EMAIL=...
   INITIAL_SUPER_ADMIN_PASSWORD=...
   ```

   Then, in the Render Shell, run:

   ```sh
   php artisan migrate --force
   php artisan db:seed --force
   ```

   Set `INITIAL_SUPER_ADMIN_ENABLED=false` and remove the name, email, and password variables immediately afterward. Later seed runs will keep roles and permissions current but will not create another administrator.

5. Set the health-check path to `/up`.
6. Verify the public storefront, administrator login, product and variant creation, Cloudinary uploads, cart, email OTP checkout, and order status transitions.

## Image handling

Products and variants store both their secure Cloudinary delivery URL and public ID. Replacement uploads a new image before changing the database, and only then attempts to delete the old asset. Failed uploads retain the existing image. Deletion accepts only public IDs under the configured folder and leaves the database record intact if Cloudinary deletion fails.

## Trial backup

Render free PostgreSQL services may expire and do not provide the backup guarantees needed for business data. Before the trial ends, run this from a trusted machine with Render’s external connection string:

```sh
pg_dump --format=custom --no-owner --file=home-pearls-trial.backup "$DATABASE_URL"
```

Store the backup securely and verify it by restoring to a separate database. Keep the Cloudinary trial folder until the business decides whether to continue with the service.

## Troubleshooting

- **Health check fails:** confirm `/up` and the Render `PORT`.
- **Database fails:** check `DB_*` variables and `DB_SSLMODE=require`.
- **Images fail:** check the three Cloudinary values and selected folder; never expose the API secret to a browser.
- **Sessions or Livewire fail:** run migrations, then `php artisan optimize:clear`.
- **Mail fails:** verify SMTP credentials and sender identity. An OTP mail failure does not create an order.
