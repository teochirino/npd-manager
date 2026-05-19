<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectTask;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, $projectId)
    {
        $tasks = ProjectTask::with(['predefinedTask.phase'])
            ->where('project_id', $projectId)
            ->get();
        
        return response()->json($tasks);
    }

    public function update(Request $request, $id)
    {
        $task = ProjectTask::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'sometimes|in:No iniciado,En progreso,Completado',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);
        
        // Actualizar progreso del proyecto
        $project = $task->project;
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'Completado')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $project->update(['progress' => $progress]);
        
        return response()->json($task->load('predefinedTask'));
    }

    public function bulkUpdate(Request $request, $projectId)
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:project_tasks,id',
            'tasks.*.status' => 'required|in:No iniciado,En progreso,Completado',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            ProjectTask::where('id', $taskData['id'])
                ->where('project_id', $projectId)
                ->update(['status' => $taskData['status']]);
        }

        // Actualizar progreso del proyecto
        $project = Project::findOrFail($projectId);
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'Completado')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $project->update(['progress' => $progress]);

        return response()->json(['message' => 'Tareas actualizadas', 'progress' => $progress]);
    }
}
