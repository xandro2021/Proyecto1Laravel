<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoanController extends Controller
{
    /**
     * Mostrar todos los préstamos
     */
    public function index()
    {
        return Loan::with(['equipment', 'user'])->get();
    }

    /**
     * Crear préstamo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'start_date' => 'nullable|date',
            'estimated_end_date' => 'required|date',
            'actual_return_date' => 'nullable|date',
            'justification' => 'nullable|string',
            'status' => 'required|in:PENDIENTE,APROBADO,RECHAZADO,PRESTADO,DEVUELTO',
            'equipment_id' => 'required|exists:equipment,id',
            'user_id' => 'required|exists:users,id',
        ]);

        return Loan::create($validated);
    }

    /**
     * Mostrar préstamo por ID
     */
    public function show(string $id)
    {
        return Loan::with(['equipment', 'user'])->findOrFail($id);
    }

    /**
     * Actualizar préstamo
     */
    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);

        $validated = $request->validate([
            'request_date' => 'sometimes|date',
            'start_date' => 'nullable|date',
            'estimated_end_date' => 'sometimes|date',
            'actual_return_date' => 'nullable|date',
            'justification' => 'nullable|string',
            'status' => 'sometimes|in:PENDIENTE,APROBADO,RECHAZADO,PRESTADO,DEVUELTO',
            'equipment_id' => 'sometimes|exists:equipment,id',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $loan->update($validated);

        return $loan;
    }

    /**
     * Eliminar préstamo
     */
    public function destroy(string $id)
    {
        Loan::destroy($id);

        return response()->noContent();
    }
}
