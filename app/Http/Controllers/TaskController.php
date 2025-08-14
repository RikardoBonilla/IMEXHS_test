<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\WeatherService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $tasks = auth()->user()->tasks()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $task = auth()->user()->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending',
            'due_date' => $request->due_date,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'user_id' => $task->user_id,
                'created_at' => $task->created_at->toISOString(),
                'updated_at' => $task->updated_at->toISOString(),
            ]
        ], 201);
    }

    public function show($id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'user_id' => $task->user_id,
                'created_at' => $task->created_at->toISOString(),
                'updated_at' => $task->updated_at->toISOString(),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update($request->only(['title', 'description', 'status', 'due_date']));

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'user_id' => $task->user_id,
                'created_at' => $task->created_at->toISOString(),
                'updated_at' => $task->updated_at->toISOString(),
            ]
        ]);
    }

    public function destroy($id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada exitosamente'
        ]);
    }

    public function getWeather($id, WeatherService $weatherService)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        if (!$task->due_date) {
            return response()->json([
                'success' => false,
                'message' => 'La tarea no tiene fecha de vencimiento'
            ], 400);
        }

        $weatherData = $weatherService->getWeatherByCity();

        if (!$weatherData['success']) {
            return response()->json($weatherData, 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date->format('Y-m-d'),
                ],
                'weather' => $weatherData['data']
            ]
        ]);
    }

    public function sendReminder($id, EmailService $emailService)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        if (!$task->due_date) {
            return response()->json([
                'success' => false,
                'message' => 'La tarea no tiene fecha de vencimiento'
            ], 400);
        }

        $result = $emailService->sendTaskReminder($task, auth()->user());

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Recordatorio enviado exitosamente',
            'data' => [
                'task_id' => $task->id,
                'sent_to' => auth()->user()->email,
                'sent_at' => now()->toISOString()
            ]
        ]);
    }
}
