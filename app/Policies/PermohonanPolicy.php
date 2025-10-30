<?php

namespace App\Policies;

use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermohonanPolicy
{
    /**
     * Cek INDEX: Boleh lihat halaman daftar permohonan apa nggak.
     * Kita cek aja salah satu, 'view' or 'view any'.
     */
    public function viewAny(User $user): bool
    {
        // Selama dia punya salah satu permission view, dia boleh buka halaman index
        return $user->can('view permohonan') || $user->can('view any permohonan');
    }

    /**
     * Cek SHOW: Boleh lihat 1 permohonan spesifik.
     */
    public function view(User $user, Permohonan $permohonan): bool
    {
        // 1. Cek permission 'any' dulu
        if ($user->can('view any permohonan')) {
            return true;
        }

        // 2. Kalo nggak punya, cek permission 'own' + kepemilikan
        return $user->can('view permohonan') && $user->id === $permohonan->user_id;
    }

    /**
     * Cek CREATE: Boleh bikin permohonan.
     */
    public function create(User $user): bool
    {
        return $user->can('create permohonan');
    }

    /**
     * Cek UPDATE: Boleh edit permohonan.
     */
    public function update(User $user, Permohonan $permohonan): bool
    {
        // 1. Cek permission 'any' dulu
        if ($user->can('edit any permohonan')) {
            return true;
        }

        // 2. Kalo nggak punya, cek permission 'own' + kepemilikan
        return $user->can('edit permohonan') && $user->id === $permohonan->user_id;
    }

    /**
     * Cek DELETE: Boleh hapus permohonan.
     */
    public function delete(User $user, Permohonan $permohonan): bool
    {
        // 1. Cek permission 'any' dulu
        if ($user->can('delete any permohonan')) {
            return true;
        }

        // 2. Kalo nggak punya, cek permission 'own' + kepemilikan
        return $user->can('delete permohonan') && $user->id === $permohonan->user_id;
    }

    /**
     * Cek STATUS: Boleh approve/reject.
     */
    public function approve(User $user): bool
    {
        // Ini cuma boleh admin, jadi kita cek permission 'approve'
        return $user->can('approve permohonan');
    }
}
