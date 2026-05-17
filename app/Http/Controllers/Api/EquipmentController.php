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
        return Equipment::all();
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

        // regla negocio
        $validated['status'] =
            $this->applyBusinessRules(
                $validated['stock'],
                $validated['status']
            );

        // guardar imagen
        if ($request->hasFile('image')) {

            $validated['image_filename'] =
                $this->imageService->saveImage(
                    $request->file('image')
                );
        }

        $equipment = Equipment::create($validated);

        return response()->json($equipment, 201);
    }

    public function show(string $id)
    {
        return Equipment::findOrFail($id);
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

        // regla negocio
        $validated['status'] =
            $this->applyBusinessRules(
                $validated['stock'],
                $validated['status']
            );


        if ($request->hasFile('image')) {

            // borrar anterior
            $this->imageService->deleteImage(
                $equipment->image_filename
            );

            // guardar nueva
            $validated['image_filename'] =
                $this->imageService->saveImage(
                    $request->file('image')
                );
        }

        $equipment->update($validated);

        return $equipment;
    }

    public function destroy(string $id)
    {
        $equipment = Equipment::findOrFail($id);

        // eliminar imagen
        $this->imageService->deleteImage(
            $equipment->image_filename
        );

        $equipment->delete();

        return response()->noContent();
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
