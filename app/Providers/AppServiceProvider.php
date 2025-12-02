<?php

namespace App\Providers;

use App\Models\Permohonan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Policies\PermohonanPolicy;
use Illuminate\Support\Facades\URL;

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
        Gate::policy(Permohonan::class, PermohonanPolicy::class);

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

        if (config('app.laravel_force_url')) {

            // 1. Paksa HTTPS (Opsional, tapi wajib kalau servernya HTTPS)
            URL::forceScheme('https');

            // 2. Paksa Root URL sesuai APP_URL di .env
            URL::forceRootUrl(config('app.url'));
        }
    }
}
