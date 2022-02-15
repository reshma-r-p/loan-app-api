<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

trait ApiHelpers
{
    protected function isAdmin($user): bool
    {
        if (!empty($user)) {
            return $user->role == 'admin';
        }

        return false;
    }

    protected function isLender($user): bool
    {

        if (!empty($user)) {
            return $user->role == 'lender';
        }

        return false;
    }

    protected function isBorrower($user): bool
    {
        if (!empty($user)) {
            return $user->role == 'borrower';
        }

        return false;
    }

    protected function isLenderOrAdmin($user): bool
    {
        if (!empty($user)) {
            return $user->role == 'lender' || $user->role == 'admin';
        }

        return false;
    }
}