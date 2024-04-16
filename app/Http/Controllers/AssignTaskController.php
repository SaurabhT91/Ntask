<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserToken;
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
            $validatedData = $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'users' => 'required|array',
                'users.*' => 'exists:users,id',
            ]);

            $task = Task::findOrFail($validatedData['task_id']);

            $task->assignedUsers()->sync($validatedData['users']);

            return response()->json(['message' => 'Task assigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to assign task. Please try again.'], 500);
        }
    }

    public function unassignTask($userId, $taskId)
    {
        try {
            $user = User::findOrFail($userId);

            $user->assignedTasks()->detach($taskId);

            return response()->json(['message' => 'Task unassigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to unassign task. Please try again.'], 500);
        }
    }

    public function tasksAssignedToCurrentUser(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['error' => 'Bearer token not provided in the request header.'], 401);
            }

            $userToken = UserToken::where('token', $token)->first();

            if (!$userToken) {
                return response()->json(['error' => 'Invalid token.'], 401);
            }

            $userId = $userToken->user_id;

            $tasks = Task::whereHas('assignedUsers', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })->get();

            return response()->json(['tasks' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch tasks.'], 500);
        }
    }
}
