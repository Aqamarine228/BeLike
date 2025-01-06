<?php

namespace App\Providers;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        $this->registerFactories();
    }

    public function registerFactories(): void
    {
        Factory::guessFactoryNamesUsing(function (string $model) {
            $model_name = Str::afterLast($model, '\\');

            if (Str::startsWith($model, 'App\\Models')) {
                return "Database\\Factories\\{$model_name}Factory";
            }

            $modules_namespace = config('modules.namespace');

            if (Str::startsWith($model, "$modules_namespace\\")) {
                $module_namespace = Str::before($model, "\\Models");
                $factories_namespace = str_replace('/', '\\', config('modules.paths.generator.factory.path'));

                return "$module_namespace\\$factories_namespace\\{$model_name}Factory";
            }

            throw new Exception("Unable to locate factory for model: $model");
        });
    }
}
