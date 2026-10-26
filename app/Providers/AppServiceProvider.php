<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rechte, die in routes/web.php mit ->middleware('can:...') verwendet werden
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('edit-grades', fn (User $user) => $user->canEditGrades());
        // Schüler- und Lehrkräfteliste: nur Admin und Lehrkräfte
        Gate::define('view-people', fn (User $user) => ! $user->isStudent());
    }
}
