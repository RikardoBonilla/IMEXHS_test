<!DOCTYPE html>
<html>
<head>
    <title>Recordatorio de Tarea</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .task-info { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Recordatorio de Tarea</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user_name }}</strong>,</p>
            
            <p>Te recordamos que tienes una tarea pendiente con fecha de vencimiento próxima:</p>
            
            <div class="task-info">
                <h3>{{ $task_title }}</h3>
                @if($task_description)
                    <p><strong>Descripción:</strong> {{ $task_description }}</p>
                @endif
                <p><strong>Fecha de vencimiento:</strong> {{ $due_date }}</p>
            </div>
            
            <p>No olvides completar tu tarea a tiempo.</p>
            
            <p>¡Saludos!</p>
        </div>
        
        <div class="footer">
            <p>Este es un email automático del sistema de gestión de tareas.</p>
        </div>
    </div>
</body>
</html>