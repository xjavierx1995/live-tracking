<?php

namespace App\Actions\Auth;

use App\Models\User;

class LogoutUserAction
{
    public function execute(User $user): void
    {
        $currentToken = $user->currentAccessToken();

        if ($currentToken && isset($currentToken->id)) {
            $user->tokens()->whereKey($currentToken->id)->delete();
        }
    }
}
