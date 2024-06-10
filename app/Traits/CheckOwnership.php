<?php

namespace App\Traits;

use App\Models\User;

trait CheckOwnership
{
    protected function checkOwnership(User $user, $model)
    {
        if ($user->id !== $model->user_id) {
            abort(401, 'Unauthorized');
        }
    }
}