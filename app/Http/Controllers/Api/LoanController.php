<?php

namespace App\Http\Controllers\Api;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => __('messages.loan_list'),
            'data' => Loan::with(['equipment', 'user'])->get()
        ]);
    }

    public function myLoans()
    {
        return response()->json([
            'message' => __('messages.loan_list'),
            'data' => Loan::with(['equipment', 'user'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'estimated_end_date' => 'required|date',
            'justification' => 'nullable|string',
            'equipment_id' => 'required|exists:equipment,id',
        ]);

        $today = Carbon::today();

        if ($today->gt(Carbon::parse($validated['estimated_end_date']))) {
            return response()->json([
                'message' => __('messages.invalid_date_range')
            ], 400);
        }

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if ($equipment->stock <= 0) {
            return response()->json([
                'message' => __('messages.no_stock')
            ], 400);
        }

        $equipment->stock -= 1;
        $equipment->applyBusinessRules();
        $equipment->save();

        $loan = Loan::create([
            'request_date' => $today,
            'estimated_end_date' => $validated['estimated_end_date'],
            'justification' => $validated['justification'] ?? null,
            'status' => 'PENDIENTE',
            'equipment_id' => $equipment->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => __('messages.loan_created'),
            'data' => $loan
        ], 201);
    }

    public function show(string $id)
    {
        $loan = Loan::with(['equipment', 'user'])->findOrFail($id);

        if (
            $loan->user_id !== Auth::id() &&
            auth()->user()->role !== 'ADMIN'
        ) {
            return response()->json([
                'message' => __('messages.unauthorized')
            ], 403);
        }

        return response()->json([
            'message' => __('messages.loan_found'),
            'data' => $loan
        ]);
    }

    public function update(Request $request, string $id)
    {
        $loan = Loan::with('equipment')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:PENDIENTE,APROBADO,RECHAZADO,PRESTADO,DEVUELTO'
        ]);

        $actual = $loan->status instanceof \App\Enums\LoanStatus
            ? $loan->status->value
            : (string) $loan->status;

        $nuevo = $validated['status'] instanceof \App\Enums\LoanStatus
            ? $validated['status']->value
            : (string) $validated['status'];

        if ($actual === $nuevo) {
            return response()->json([
                'message' => __('messages.no_changes')
            ]);
        }

        $this->validarTransicion($actual, $nuevo);

        $equipment = $loan->equipment;

        if (
            in_array($actual, ['PENDIENTE', 'APROBADO']) &&
            $nuevo === 'RECHAZADO'
        ) {
            $equipment->stock += 1;
        }

        if ($actual === 'PRESTADO' && $nuevo === 'DEVUELTO') {
            $equipment->stock += 1;
            $loan->actual_return_date = Carbon::today();
        }

        if ($actual === 'RECHAZADO' && $nuevo === 'PENDIENTE') {
            if ($equipment->stock <= 0) {
                return response()->json([
                    'message' => __('messages.no_stock_re_evaluation')
                ], 400);
            }

            $equipment->stock -= 1;
        }

        if ($actual === 'APROBADO' && $nuevo === 'PRESTADO') {
            $loan->start_date = Carbon::today();
        }

        $equipment->applyBusinessRules();
        $equipment->save();

        $loan->status = $nuevo;
        $loan->save();
        $loan->refresh();
        $loan->status = $loan->status instanceof LoanStatus
            ? $loan->status->value
            : $loan->status;

        return response()->json([
            'message' => __('messages.loan_updated'),
            'data' => $loan
        ]);
    }

    public function destroy(string $id)
    {
        $loan = Loan::with('equipment')->findOrFail($id);

        $equipment = $loan->equipment;

        // 🔥 devolver stock si aplica
        if (in_array($loan->status, ['PENDIENTE', 'APROBADO', 'PRESTADO'])) {
            $equipment->stock += 1;
            $equipment->applyBusinessRules();
            $equipment->save();
        }

        $loan->delete();

        return response()->json([
            'message' => __('messages.loan_deleted')
        ]);
    }

    private function validarTransicion($actual, $nuevo)
    {
        if ($actual === $nuevo) return;

        $map = [
            'PENDIENTE' => ['APROBADO', 'RECHAZADO'],
            'APROBADO'  => ['PRESTADO', 'RECHAZADO'],
            'PRESTADO'  => ['DEVUELTO'],
            'RECHAZADO' => ['PENDIENTE'],
            'DEVUELTO'  => []
        ];

        if (!in_array($nuevo, $map[$actual])) {
            throw new \Exception("Transición inválida: $actual → $nuevo");
        }
    }
}
