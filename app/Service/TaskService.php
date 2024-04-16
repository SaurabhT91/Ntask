<?php

namespace App\Service;

use App\Models\Task;

class TaskService
{
    public function getTasks()
    {
        return Task::all();
    }
}
