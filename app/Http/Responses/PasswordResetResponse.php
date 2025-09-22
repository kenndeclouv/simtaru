<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Laravel\Fortify\Fortify;

class PasswordResetResponse implements PasswordResetResponseContract
{
    public function toResponse($request)
    {
        return redirect()->intended(Fortify::redirects('password-updated', route('login')))
            ->with('success', 'Password Anda berhasil diperbarui! Silakan login kembali.');
    }
}
