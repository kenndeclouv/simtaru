<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        /**
         * Mengubah bahasa Carbon menjadi bahasa yang sesuai dengan APP_LOCALE di env
         */
        Carbon::setLocale(env('APP_LOCALE', 'id'));

        /**
         * Dokumentasi Penggunaan Direktif Blade @errorFeedback
         *
         * Direktif ini digunakan untuk menampilkan umpan balik kesalahan
         * pada form input. Jika ada kesalahan validasi untuk field tertentu,
         * direktif ini akan menampilkan pesan kesalahan di bawah input.
         *
         * Cara menggunakan:
         * 1. Pastikan Kamu telah menambahkan validasi pada controller
         *    sebelum mengembalikan tampilan.
         * 2. Di dalam file Blade Kamu, gunakan direktif ini dengan
         *    menyertakan nama field yang ingin Kamu periksa.
         *
         * Contoh penggunaan:
         *
         * <input type="text" name="username" class="form-control @error('username') is-invalid @enderror">
         * @errorFeedback('username')
         *
         * Dalam contoh di atas, jika ada kesalahan validasi untuk field
         * 'username', maka pesan kesalahan akan ditampilkan di bawah input
         * dengan kelas 'invalid-feedback'.
         */
        Blade::directive('errorFeedback', function ($field) {
            return "<?php if(\$errors->has($field)): ?>
                <div class='invalid-feedback'>{{ \$errors->first($field) }}</div>
            <?php endif; ?>";
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
