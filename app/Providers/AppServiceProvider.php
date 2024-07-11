<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Validator;

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
        Schema::defaultStringLength(191);

        Validator::extend('sa_mobile', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(966|\\+966|0)?5[0-9]{8}$/', $value);
        });

        Validator::replacer('sa_mobile', function ($message, $attribute, $rule, $parameters) {
            return 'mobile is not a valid Saudi Arabia mobile number.';
        });


        Builder::macro('betweenEqual', function ($field, $array) {
            return $this->where($field, '>=', $array[0])
                ->where($field, '<=', $array[1]);
        });

        Builder::macro('search', function ($field, $string) {
            return $string ? $this->where($field, 'like', '%' . $string . '%') : $this;
        });

        Builder::macro('orSearch', function ($field, $string) {
            return $string ? $this->orWhere($field, 'like', '%' . $string . '%') : $this;
        });

        Builder::macro('active', function () {
            return $this->where('is_active', 1);
        });

        Builder::macro('inActive', function () {
            return $this->where('is_active', 0);
        });
    }
}
