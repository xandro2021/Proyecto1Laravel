<?php

namespace App\Http\Controllers\Api;

use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EquipmentImageService;

class EquipmentController extends Controller
{
    private EquipmentImageService $imageService;

    public function __construct(EquipmentImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        return response()->json([
            'message' => __('messages.equipment_list'),
            'data' => Equipment::all()
        ]);
    }

    public function show(string $id)
    {
        $equipment = Equipment::findOrFail($id);

        return response()->json([
            'message' => __('messages.equipment_found'),
            'data' => $equipment
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:DISPONIBLE,OCUPADO,MANTENIMIENTO',
            'image' => 'nullable|image|max:2048'
        ]);

        $equipment = new Equipment();
        $equipment->fill($validated);

        if ($request->hasFile('image')) {
            $equipment->image_filename =
                $this->imageService->saveImage($request->file('image'));
        }

        // 🔥 regla centralizada en el modelo
        $equipment->applyBusinessRules();

        $equipment->save();

        return response()->json([
            'message' => __('messages.equipment_created'),
            'data' => $equipment
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:DISPONIBLE,OCUPADO,MANTENIMIENTO',
            'image' => 'nullable|image|max:2048'
        ]);

        $equipment->fill($validated);

        if ($request->hasFile('image')) {

            if ($equipment->image_filename) {
                $this->imageService->deleteImage($equipment->image_filename);
            }

            $equipment->image_filename =
                $this->imageService->saveImage($request->file('image'));
        }

        // 🔥 regla de negocio SIEMPRE después de cambios
        $equipment->applyBusinessRules();

        $equipment->save();

        return response()->json([
            'message' => __('messages.equipment_updated'),
            'data' => $equipment
        ]);
    }

    public function destroy(string $id)
    {
        $equipment = Equipment::findOrFail($id);

        if ($equipment->image_filename) {
            $this->imageService->deleteImage($equipment->image_filename);
        }

        $equipment->delete();

        return response()->json([
            'message' => __('messages.equipment_deleted')
        ]);
    }
}
