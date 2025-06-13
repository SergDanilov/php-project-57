<?php

namespace App\Policies;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, TaskStatus $status): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TaskStatus $status): bool
    {
        return true;
    }

    public function delete(User $user, TaskStatus $status): bool
    {
        return true;
    }

    public function restore(User $user, TaskStatus $status): bool
    {
        return true;
    }

    public function forceDelete(User $user, TaskStatus $status): bool
    {
        return $this->delete($user, $status);
    }
}
