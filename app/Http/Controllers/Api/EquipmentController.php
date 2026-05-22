<?php

namespace App\Http\Controllers\Api;

use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EquipmentImageService;

class EquipmentController extends Controller
{
    private EquipmentImageService $imageService;

    public function __construct(
        EquipmentImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

    public function index()
    {
        return response()->json([
            'message' => __('messages.equipment_list'),
            'data' => Equipment::all()
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

        $validated['status'] =
            $this->applyBusinessRules(
                $validated['stock'],
                $validated['status']
            );

        if ($request->hasFile('image')) {

            $validated['image_filename'] =
                $this->imageService->saveImage(
                    $request->file('image')
                );
        }

        $equipment = Equipment::create($validated);

        return response()->json([
            'message' => __('messages.equipment_created'),
            'data' => $equipment
        ], 201);
    }

    public function show(string $id)
    {
        $equipment = Equipment::findOrFail($id);

        return response()->json([
            'message' => __('messages.equipment_found'),
            'data' => $equipment
        ]);
    }

    public function update(
        Request $request,
        string $id
    ) {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:DISPONIBLE,OCUPADO,MANTENIMIENTO',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['status'] =
            $this->applyBusinessRules(
                $validated['stock'],
                $validated['status']
            );

        if ($request->hasFile('image')) {

            $this->imageService->deleteImage(
                $equipment->image_filename
            );

            $validated['image_filename'] =
                $this->imageService->saveImage(
                    $request->file('image')
                );
        }

        $equipment->update($validated);

        return response()->json([
            'message' => __('messages.equipment_updated'),
            'data' => $equipment
        ]);
    }

    public function destroy(string $id)
    {
        $equipment = Equipment::findOrFail($id);

        $this->imageService->deleteImage(
            $equipment->image_filename
        );

        $equipment->delete();

        return response()->json([
            'message' => __('messages.equipment_deleted')
        ]);
    }

    private function applyBusinessRules(
        int $stock,
        string $status
    ): string {

        if ($stock <= 0) {
            return 'OCUPADO';
        }

        if ($status !== 'MANTENIMIENTO') {
            return 'DISPONIBLE';
        }

        return $status;
    }
}
