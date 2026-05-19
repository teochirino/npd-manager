<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sku;
use Illuminate\Http\Request;

class SkuController extends Controller
{
    public function index($projectId)
    {
        $skus = Sku::where('project_id', $projectId)->get();
        return response()->json($skus);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'sku_id' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'status' => 'sometimes|in:Pendiente,En proceso,Completado',
            'checklist' => 'nullable|array',
        ]);

        $sku = Sku::create($validated);
        
        return response()->json($sku, 201);
    }

    public function update(Request $request, $id)
    {
        $sku = Sku::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:Pendiente,En proceso,Completado',
            'checklist' => 'nullable|array',
        ]);

        $sku->update($validated);
        
        return response()->json($sku);
    }

    public function destroy($id)
    {
        $sku = Sku::findOrFail($id);
        $sku->delete();
        
        return response()->json(['message' => 'SKU eliminado correctamente']);
    }
}
