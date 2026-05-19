<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\PredefinedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Project::with(['engineer', 'tasks', 'skus', 'gateApprovals'])
            ->orderBy('created_at', 'desc');
        
        // Filtrar según el rol del usuario
        if ($user->isIngeniero()) {
            $query->where('engineer_id', $user->id);
        }
        
        // Filtros adicionales
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('phase')) {
            $query->where('active_phase', $request->phase);
        }
        
        if ($request->has('engineer_id')) {
            $query->where('engineer_id', $request->engineer_id);
        }
        
        $projects = $query->get();
        
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|string|max:20|unique:projects',
            'name' => 'required|string|max:255',
            'engineer_id' => 'required|exists:users,id',
            'final_date' => 'nullable|date',
            'status' => 'nullable|in:En linea,En pausa',
            'category' => 'nullable|string|max:100',
            'brief' => 'nullable|string',
        ]);

        // Establecer valores por defecto
        $validated['active_phase'] = 'Diseño';
        $validated['status'] = $validated['status'] ?? 'En linea';
        $validated['progress'] = 0;
        $validated['phase_date'] = $validated['final_date'] ?? now();

        $project = Project::create($validated);
        
        // Crear tareas predefinidas para TODAS las fases
        $this->createAllPhaseTasks($project);
        
        // Si hay brief, crear nota inicial
        if (!empty($validated['brief'])) {
            $project->notes()->create([
                'user_id' => $request->user()->id,
                'content' => $validated['brief'],
            ]);
        }
        
        return response()->json($project->load(['engineer', 'tasks']), 201);
    }

    public function show($id)
    {
        $project = Project::with([
            'engineer',
            'tasks.predefinedTask.phase',
            'skus',
            'gateApprovals',
            'notes.user'
        ])->findOrFail($id);
        
        return response()->json($project);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'engineer_id' => 'sometimes|exists:users,id',
            'final_date' => 'nullable|date',
            'progress' => 'sometimes|integer|min:0|max:100',
            'status' => 'sometimes|in:Terminado,En linea,En pausa,Cancelado,Fuera de linea',
            'active_phase' => 'sometimes|in:Diseño,Desarrollo,Implementacion,Lanzamiento,Seguimiento',
            'phase_date' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        $project->update($validated);
        
        return response()->json($project->load(['engineer', 'tasks']));
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        
        return response()->json(['message' => 'Proyecto eliminado correctamente']);
    }

    public function updateProgress($id)
    {
        $project = Project::findOrFail($id);
        
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'Completado')->count();
        
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        $project->update(['progress' => $progress]);
        
        return response()->json(['progress' => $progress]);
    }

    private function createTasksForPhase(Project $project)
    {
        $phase = DB::table('phases')->where('name', $project->active_phase)->first();
        
        if ($phase) {
            $predefinedTasks = PredefinedTask::where('phase_id', $phase->id)->get();
            
            foreach ($predefinedTasks as $task) {
                ProjectTask::create([
                    'project_id' => $project->id,
                    'task_predefined_id' => $task->id,
                    'status' => 'No iniciado',
                ]);
            }
        }
    }

    private function createAllPhaseTasks(Project $project)
    {
        // Obtener todas las fases
        $phases = DB::table('phases')->get();
        
        foreach ($phases as $phase) {
            // Obtener todas las tareas predefinidas de cada fase
            $predefinedTasks = PredefinedTask::where('phase_id', $phase->id)->get();
            
            foreach ($predefinedTasks as $task) {
                ProjectTask::create([
                    'project_id' => $project->id,
                    'task_predefined_id' => $task->id,
                    'status' => 'No iniciado',
                ]);
            }
        }
    }

    public function changePhase(Request $request, $id)
    {
        $validated = $request->validate([
            'phase' => 'required|in:Diseño,Desarrollo,Implementacion,Lanzamiento,Seguimiento',
        ]);

        $project = Project::findOrFail($id);
        $project->update([
            'active_phase' => $validated['phase'],
            'phase_date' => now(),
        ]);

        // Crear tareas para la nueva fase
        $this->createTasksForPhase($project);

        return response()->json($project->load(['tasks']));
    }
}
