<?php

/**
 * Format penulisan fungsi
 *
 * 1. Nama fungsi RELATION harus menggunakan PascalCase. Agar membedakannya dengan nama kolom.
 * 2. Parameter fungsi harus didefinisikan dengan tipe data yang jelas.
 * 3. Gunakan penamaan yang deskriptif untuk fungsi dan parameter.
 *
 * Contoh:
 *
 * public function SomeRelations()
 * {
 *   return $this->hasMany(SomeRelation::class);
 * }
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Feature;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'photo',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    private function getConsistentColor()
    {
        $hash = md5($this->name ?? 'Averroes');
        $color = substr($hash, 0, 6);

        return $color;
    }
    public function getPhotoAttribute($value)
    {
        if (!empty($value) && !is_null($value)) {
            return $value;
        }
        $color = $this->getConsistentColor();
        $name = $this->name ?? 'Averroes';

        return "https://api.dicebear.com/6.x/initials/svg?seed=" . urlencode($name) . "&backgroundColor=" . $color;
    }
}
