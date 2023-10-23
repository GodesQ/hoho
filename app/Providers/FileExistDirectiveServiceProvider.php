<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;
use File;

class FileExistDirectiveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('fileExists', function ($expression) {
            return "<?php if (File::exists(public_path($expression))): ?>";
        });

        Blade::directive('elsefileExists', function () {
            return '<?php else: ?>';
        });

        Blade::directive('endfileExists', function () {
            return '<?php endif; ?>';
        });
    }
}
