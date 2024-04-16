<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Service\TaskService;
use App\Models\UserToken;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Import Response class
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        $tasks = $this->taskService->getTasks();
        return response()->json($tasks);
    }

    public function changeTaskStatus(Request $request, $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);

            $token = $request->bearerToken();


            $userToken = UserToken::where('token', $token)->first();

            if (!$userToken) {
                return response()->json(['error' => 'Invalid token.'], 401);
            }

            $userId = $userToken->user_id;

            if (!$task->assignedUsers()->where('user_id', $userId)->exists()) {
                return response()->json(['error' => 'You are not assigned to this task.'], 403);
            }

            $validatedData = $request->validate([
                'status' => 'required|in:pending,completed',
            ]);

            $task->update(['status' => $validatedData['status']]);

            return response()->json(['message' => 'Task status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update task status.'], 500);
        }
    }



    public function store(Request $request)
    {
        try {
            // Retrieve token from request headers
            $token = $request->bearerToken();

            // Query user_tokens table to find user ID for the token
            $userToken = UserToken::where('token', $token)->first();

            // Check if token exists and is associated with a user
            if (!$userToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token',
                ], 401);
            }

            // Get the user ID from the user_token
            $userId = $userToken->user_id;

            // Check if the user exists
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 401);
            }

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
            ]);

            $validatedData['created_by'] = $userId; // Assign the user ID

            $task = Task::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Task creation failed',
            ], 422);
        }
    }



    public function assignTask(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|array',
            'user_id.*' => 'exists:users,id',
        ]);

        $task->assignedUsers()->sync($validatedData['user_id']);

        return response()->json(['success' => true, 'message' => 'Task assigned successfully!'], 200);
    }

    public function taskList(Request $request)
    {
        try {
            $query = Task::query();

            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('filter_date')) {
                $query->whereDate('due_date', $request->filter_date);
            }

            if ($request->has('assigned_user') && $request->assigned_user !== '') {
                $query->whereHas('assignedUsers', function ($query) use ($request) {
                    $query->where('user_id', $request->assigned_user);
                });
            }

            $tasks = $query->get();

            return response()->json(['tasks' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch tasks.'], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            // Retrieve token from request headers
            $token = $request->bearerToken();

            // Query user_tokens table to find user ID for the token
            $userToken = UserToken::where('token', $token)->first();

            // Check if token exists and is associated with a user
            if (!$userToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token',
                ], 401);
            }

            // Get the user ID from the user_token
            $userId = $userToken->user_id;

            // Check if the user exists
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 401);
            }

            // Check if the task exists
            $task = Task::find($id);
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found',
                ], 404);
            }

            // Check if the user is authorized to update the task
            if ($userId !== $task->created_by) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validatedData = $request->validate([
                'title' => 'string|max:255',
                'description' => 'string',
                'due_date' => 'date',
                'status' => 'string|in:pending,completed',
            ]);

            $task->update($validatedData);

            return response()->json(['success' => true, 'message' => 'Task updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['success' => true, 'message' => 'Task deleted successfully!'], 200);
    }
}
