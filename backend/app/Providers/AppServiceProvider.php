<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //blade tags for laracancan
        Blade::directive('isLogin', function() {
            return "<?php if(Auth::check()):?>";
        });
        Blade::directive('endisLogin', function()
        {
            return '<?php endif; ?>';
        });
        Blade::directive('isLogout', function() {
            return "<?php if(Auth::guest()):?>";
        });
        Blade::directive('endisLogout', function()
        {
            return '<?php endif; ?>';
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
