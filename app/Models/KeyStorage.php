<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyStorage extends Model
{
    protected $fillable = ['var_key', 'var_value', 'var_description'];
}
