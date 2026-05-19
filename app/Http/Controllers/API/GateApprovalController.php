<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GateApproval;
use Illuminate\Http\Request;

class GateApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = GateApproval::with(['project.engineer', 'approver']);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $approvals = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($approvals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'gate_key' => 'required|string',
            'items_checked' => 'nullable|array',
            'comments' => 'nullable|string',
        ]);

        $validated['status'] = 'pendiente';
        
        $approval = GateApproval::updateOrCreate(
            [
                'project_id' => $validated['project_id'],
                'gate_key' => $validated['gate_key']
            ],
            $validated
        );
        
        return response()->json($approval, 201);
    }

    public function approve(Request $request, $id)
    {
        $approval = GateApproval::findOrFail($id);
        
        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        $approval->update([
            'status' => 'aprobado',
            'approved_date' => now(),
            'approved_by' => $request->user()->id,
            'comments' => $validated['comments'] ?? $approval->comments,
        ]);
        
        return response()->json($approval->load(['approver']));
    }

    public function reject(Request $request, $id)
    {
        $approval = GateApproval::findOrFail($id);
        
        $validated = $request->validate([
            'comments' => 'required|string',
        ]);

        $approval->update([
            'status' => 'rechazado',
            'approved_by' => $request->user()->id,
            'comments' => $validated['comments'],
        ]);
        
        return response()->json($approval->load(['approver']));
    }
}
