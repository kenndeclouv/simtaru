<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as ResponseContract;

class PasswordResetLinkSentResponse implements ResponseContract
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // Daripada mengambil dari session, kita tulis pesannya langsung di sini.
        // Ini lebih aman karena tidak memberitahu apakah email itu terdaftar atau tidak.
        return back()->with('success', 'Jika email Anda terdaftar, kami telah mengirimkan link untuk mereset password!');
    }
}
