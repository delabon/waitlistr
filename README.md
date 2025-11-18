# A "Coming Soon" Waitlist Starter Kit

You have a revolutionary SaaS idea that's going to change the world, but it's still in the early phase, and you want to capture potential customers' emails?
**Waitlistr** will help you market your idea even before it's ready for the public.

Think of **Waitlistr** as the foundation for your next SaaS platform.

### Tech Stack
- PHP 8.4 & Laravel 12
- Web Components: Tailwind 4, Vue 3, Inertia 2, Vite
- DBs: MySQL 8, Redis (Pub/Sub, Cache, & Queues)
- Testing: Pest 4 (+ browser testing)
- CI: GitHub Actions
- Code Quality: Pint, LaraStan(PHPStan - Level Max)
- Dev tools: Sail (Docker), Telescope, Horizon

### Installation

1. Clone the repo

```shell
git clone git@github.com:delabon/waitlistr.git
cd waitlistr
```

2. Setup

```shell
composer install
vendor/bin/sail up --build -d
cp .env.example .env
vendor/bin/sail artisan key:generate
```

3. Run the migration scripts

```shell
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed
```

4. Build the assets

```shell
vendor/bin/sail npm install
vendor/bin/sail npm run build
```

5. Start the queue worker

```shell
vendor/bin/sail artisan queue:work -v
```

6. Check out the app

http://localhost/

To sign-in as an admin, make sure to run the corresponding seeder script (see installation note 3) and then log-in using `admin@test.com/12345678`.
To sign-in as a regular user use these credentials `test@test.com/12345678`

http://localhost/login

### Run all tests

```shell
vendor/bin/sail composer test
```

### Run Pest

```shell
vendor/bin/sail composer test:pest
```

### Run LaraStan (PHPStan)

```shell
vendor/bin/sail composer test:stan
```

### Run Pint

```shell
vendor/bin/sail composer test:pint
```

### Why and How

For user roles I could've used the Spatie Permissions package, but for the sake of simplicity, I just added a role field to the users table with a UserRole enum (Admin or User).
