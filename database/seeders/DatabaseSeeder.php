<?php

namespace Database\Seeders;

use App\Models\KeyStorage;
use App\Models\Permission;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class
        ]);

        $superadmin = User::create([
            'name' => 'superadmin',
            'email' => 'super@admin.com',
            'password' => 'superadmin',
        ]);

        $superadmin->assignRole('Super Admin');

        $user = User::create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => 'user',
        ]);

        $user->assignRole('User');

        KeyStorage::create([
            'var_key' => 'preFixNomorSurat',
            'var_value' => '600.3/',
            'var_description' => 'Prefix nomor surat',
        ]);
        KeyStorage::create([
            'var_key' => 'preFixNomorPermohonan',
            'var_value' => '600.3/',
            'var_description' => 'Prefix nomor permohonan',
        ]);
        KeyStorage::create([
            'var_key' => 'postFixNomorSurat',
            'var_value' => '/ITR/427.56/',
            'var_description' => 'Postfix nomor surat',
        ]);
        KeyStorage::create([
            'var_key' => 'postFixNomorPermohonan',
            'var_value' => '/Permohonan.SITR/427.56/',
            'var_description' => 'Postfix nomor permohonan',
        ]);
        KeyStorage::create([
            'var_key' => 'provinsiUsahaDefaultId',
            'var_value' => '35',
            'var_description' => 'Provinsi usaha default id',
        ]);
        KeyStorage::create([
            'var_key' => 'kabupatenUsahaDefaultId',
            'var_value' => '3508',
            'var_description' => 'Kabupaten usaha default id',
        ]);
    }
}
