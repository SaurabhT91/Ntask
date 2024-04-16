<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class AssignTaskController extends Controller
{
    public function showUserList()
    {
        $usersWithTasks = User::with('assignedTasks')->paginate(10);

        return response()->json($usersWithTasks);
    }

    public function getUserWithTasks($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $tasks = $user->assignedTasks()->get();

           
            return response()->json([
                'user' => $user,
                'tasks' => $tasks
            ]);
        } catch (\Exception $e) {
           
            return response()->json(['error' => 'Failed to fetch user and tasks.'], 500);
        }
    }

    public function fetchUserList()
    {
        $users = User::all(['id', 'name']);

        $response = response()->json($users);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        return $response;
    }

    public function showAssignTaskForm(Task $task)
    {
        $taskId = $task->id;
        $users = User::all();

        return response()->json(['task' => $task, 'users' => $users]);
    }

    public function assign(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'users' => 'required|array',
                'users.*' => 'exists:users,id',
            ]);

            // Find the task by ID
            $task = Task::findOrFail($validatedData['task_id']);

            // Attach users to the task
            $task->assignedUsers()->sync($validatedData['users']);

            // Return success response
            return response()->json(['message' => 'Task assigned successfully']);
        } catch (\Exception $e) {
            // Handle any errors and return error response
            return response()->json(['error' => 'Failed to assign task. Please try again.'], 500);
        }
    }

    public function unassignTask($userId, $taskId)
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($userId);

            // Detach the task from the user
            $user->assignedTasks()->detach($taskId);

            // Return success response
            return response()->json(['message' => 'Task unassigned successfully']);
        } catch (\Exception $e) {
            // Handle any errors and return error response
            return response()->json(['error' => 'Failed to unassign task. Please try again.'], 500);
        }
    }
}
