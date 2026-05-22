<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Equipment;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    /**
     * Mostrar todos los préstamos
     */
    public function index()
    {
        return Loan::with(['equipment', 'user'])->get();
    }

    public function myLoans()
    {
        return Loan::with(['equipment', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    /**
     * Crear préstamo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'estimated_end_date' => 'required|date',
            'justification' => 'nullable|string',
            'equipment_id' => 'required|exists:equipment,id',
        ]);

        $today = Carbon::today();

        // validar fechas (Spring: requestDate vs estimatedEndDate)
        if ($today->gt(Carbon::parse($validated['estimated_end_date']))) {
            return response()->json([
                'message' => 'Rango de fechas inválido'
            ], 400);
        }

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        // validar stock
        if ($equipment->stock <= 0) {
            return response()->json([
                'message' => 'Sin stock'
            ], 400);
        }

        // ↓ reducir stock (igual que Spring)
        $equipment->stock -= 1;
        $equipment->save();

        $loan = Loan::create([
            'request_date' => $today,
            'estimated_end_date' => $validated['estimated_end_date'],
            'justification' => $validated['justification'] ?? null,
            'status' => 'PENDIENTE',
            'equipment_id' => $equipment->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json($loan, 201);
    }

    /**
     * Mostrar préstamo por ID
     */
    public function show(string $id)
    {
        $loan = Loan::with(['equipment', 'user'])->findOrFail($id);

        if ($loan->user_id !== Auth::id() && auth()->user()->role !== 'ADMIN') {
            abort(403, 'No autorizado');
        }

        return $loan;
    }

    /**
     * Actualizar préstamo
     */
    public function update(Request $request, string $id)
    {
        $loan = Loan::with('equipment')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:PENDIENTE,APROBADO,RECHAZADO,PRESTADO,DEVUELTO'
        ]);

        $nuevo = $validated['status'];
        $actual = $loan->status;

        // validar transición (igual que Spring)
        $this->validarTransicion($actual, $nuevo);

        $equipment = $loan->equipment;

        // =========================
        // EFECTOS DE TRANSICIÓN
        // =========================

        // RECHAZADO → devolver stock
        if (
            in_array($actual, ['PENDIENTE', 'APROBADO']) &&
            $nuevo === 'RECHAZADO'
        ) {
            $equipment->stock += 1;
            $equipment->save();
        }

        // APROBADO → PRESTADO
        if ($actual === 'APROBADO' && $nuevo === 'PRESTADO') {
            $loan->start_date = Carbon::today();
        }

        // PRESTADO → DEVUELTO
        if ($actual === 'PRESTADO' && $nuevo === 'DEVUELTO') {
            $equipment->stock += 1;
            $equipment->save();

            $loan->actual_return_date = Carbon::today();
        }

        // RECHAZADO → PENDIENTE (reevaluación)
        if ($actual === 'RECHAZADO' && $nuevo === 'PENDIENTE') {

            if ($equipment->stock <= 0) {
                return response()->json([
                    'message' => 'No hay stock para reevaluar'
                ], 400);
            }

            $equipment->stock -= 1;
            $equipment->save();
        }

        $loan->status = $nuevo;
        $loan->save();

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

    private function validarTransicion($actual, $nuevo)
    {
        if ($actual === $nuevo) return;

        switch ($actual) {
            case 'PENDIENTE':
                if (!in_array($nuevo, ['APROBADO', 'RECHAZADO'])) {
                    abort(400, "Transición inválida: $actual → $nuevo");
                }
                break;

            case 'APROBADO':
                if (!in_array($nuevo, ['PRESTADO', 'RECHAZADO'])) {
                    abort(400, "Transición inválida: $actual → $nuevo");
                }
                break;

            case 'PRESTADO':
                if ($nuevo !== 'DEVUELTO') {
                    abort(400, "Transición inválida: $actual → $nuevo");
                }
                break;

            case 'RECHAZADO':
                if ($nuevo !== 'PENDIENTE') {
                    abort(400, "Transición inválida: $actual → $nuevo");
                }
                break;

            case 'DEVUELTO':
                abort(400, "No se puede modificar un préstamo DEVUELTO");
        }
    }
}
