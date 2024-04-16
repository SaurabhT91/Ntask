<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    use HasFactory;

    protected $table = 'task_user';
    protected $fillable = [
        'task_id', 'user_id',
    ];

    /**
     * Get the task that belongs to the user.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user that belongs to the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
