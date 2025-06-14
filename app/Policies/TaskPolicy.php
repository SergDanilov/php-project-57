<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Task $task): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return true;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->creator()->is($user);
    }

    public function restore(User $user, Task $task): bool
    {
        return true;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $task->creator()->is($user);
    }
}
