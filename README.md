# ⚡ PHP TemplateKit

> A **lightweight PHP template engine** with Blade-like syntax, reusable components, layouts, custom routing, and a powerful CLI.  
> Built for developers who value simplicity, speed, and full control.

---

## 🧩 Features

✅ Blade-style syntax: `@layout`, `@component`, `@config`, `@csrf`  
✅ Custom PHP CLI: `php kit make:controller`, `make:layout`, `make:component`, etc.  
✅ Clean routing system with `url()` and `route()` helpers  
✅ Page components, layout injection, and config loading  
✅ Supports **Bootstrap**, **TailwindCSS**, or your custom styles  
✅ Easily extendable — no framework bloat!

---

## 🚀 Quick Start

```bash
git clone https://github.com/yourname/templatekit.git
cd templatekit
composer install



templatekit/
├── app/
│   ├── Controllers/           # All controllers
│   └── Core/                  # Custom engine: Blade, Router, Template, etc.
├── config/
│   └── site.php               # Site-wide settings (title, base_url, etc.)
├── public/
│   └── index.php              # Entry point for the application
├── resources/
│   ├── views/
│   │   ├── layouts/           # Master layouts
│   │   ├── pages/             # All views (.blade.php)
│   │   └── components/        # Reusable Blade components
├── routes/
│   └── web.php                # All route definitions
├── storage/
│   └── framework/views/       # Parsed Blade templates
└── kit                       # CLI command handler



🛠️ CLI Commands
Easily scaffold your components via the CLI:

php kit make:controller Auth/AuthController
php kit make:layout home/authlayout
php kit make:component Header/navbar
php kit make:page dashboard



🌐 Routing
Define routes in routes/web.php:

use App\Core\Route;

Route::get('/', function () {
    return view('welcome')->with('title', 'Welcome Page');
}, 'welcome');

Route::get('/login', [AuthController::class, 'index'], 'login');

Use in your Blade views:
<a href="<?= url('login') ?>">Login</a>
<a href="<?= route('login') ?>">Go to Login</a>



📄 Blade Syntax Reference

| Directive      | Usage                                     |
| -------------- | ----------------------------------------- |
| `@layout()`    | Set the layout view                       |
| `@component()` | Inject a component Blade view             |
| `@config()`    | Pull from `config/site.php`               |
| `$asset.css$`  | Auto-load assets from `resources/assets/` |
| `@csrf`        | Inserts CSRF input field                  |


Example:

@layout('layouts.master')

@component('Header.navbar')

<div class="text-center py-5">
    <h1>Welcome to PHP TemplateKit</h1>
    <p>Your lightweight templating engine</p>
</div>


🔧 Global Helper Functions
url('path')           // → returns full URL
route('route_name')   // → returns named route URL
config('site.key')    // → fetch config value
view('page')          // → returns ViewResponse object

Chaining support:
return view('auth.index')->with('title', 'Login Page');

🧠 Example View (welcome.blade.php)
@layout('layouts.master')

<div class="container text-center py-5">
    <h1 class="display-4">👋 Welcome to PHP TemplateKit</h1>
    <p class="lead">Build custom PHP apps faster than ever.</p>
    <a href="<?= route('login') ?>" class="btn btn-primary mt-3">Get Started</a>
</div>

⚙️ Configuration (config/site.php)
return [
    'base_url' => 'http://localhost/templatekit',
    'default_title' => 'TemplateKit',
    'default_description' => 'Lightweight PHP framework for templated websites',
];

💡 Future Features
🧠 View caching (no re-parsing unless modified)

🛡️ Middleware for auth and guards

📦 Plugin support

🎨 Blade directive extensions (@if, @foreach, etc.)

🧰 CLI for model & service generators
