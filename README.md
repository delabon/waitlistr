# A "Coming Soon" Waitlist App

You have a revolutionary SaaS idea that's going to change the world, but it's still in the early phase, and you want to capture potential customers' emails?
**Waitlistr** will help you market your idea even before it's ready for the public.

Think of **Waitlistr** as the foundation for your next SaaS platform.

### Tech Stack
- PHP 8.4 & Laravel 12
- Web Components: Tailwind 4, Vue 3, Inertia 2, Vite
- DBs: MySQL 8, Redis (Cache & Queues)
- Testing: Pest 4
- CI: GitHub Actions
- Code Quality: Pint, LaraStan(PHPStan - Level Max)
- Dev tools: Sail (Docker), Telescope, Horizon

### Installation



### Why and How

In your Laravel Conventions guide, you said to use modules pattern when you have > 100 models. So in this project I'll not use modules, just to keep it simple.

For user roles I could've used the Spatie Permissions package, but for the sake of simplicity, I just added a role field to the users table with a UserRole enum (Admin or User for now).
