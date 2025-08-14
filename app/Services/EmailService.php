<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function sendTaskReminder($task, $user)
    {
        try {
            $data = [
                'user_name' => $user->name,
                'task_title' => $task->title,
                'task_description' => $task->description,
                'due_date' => $task->due_date->format('d/m/Y'),
            ];

            Mail::send('emails.task-reminder', $data, function ($message) use ($user, $task) {
                $message->to($user->email, $user->name)
                        ->subject('Recordatorio de Tarea: ' . $task->title);
            });

            return [
                'success' => true,
                'message' => 'Email enviado exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error('Error enviando email: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al enviar el email'
            ];
        }
    }
}