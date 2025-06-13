<?php

namespace App\Policies;

use App\Models\Label;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LabelPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Label $label): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Label $label): bool
    {
        return true;
    }

    public function delete(User $user, Label $label): bool
    {
        return true;
    }

    public function restore(User $user, Label $label): bool
    {
        return true;
    }

    public function forceDelete(User $user, Label $label): bool
    {
        return true;
    }
}
