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

        $nuevo = $validated['status'];
        $actual = $loan->status;

        $error = $this->validarTransicion($actual, $nuevo);

        if ($error) {
            return response()->json([
                'message' => $error
            ], 400);
        }

        $equipment = $loan->equipment;

        if (
            in_array($actual, ['PENDIENTE', 'APROBADO']) &&
            $nuevo === 'RECHAZADO'
        ) {
            $equipment->stock += 1;
            $equipment->save();
        }

        if ($actual === 'APROBADO' && $nuevo === 'PRESTADO') {
            $loan->start_date = Carbon::today();
        }

        if ($actual === 'PRESTADO' && $nuevo === 'DEVUELTO') {

            $equipment->stock += 1;
            $equipment->save();

            $loan->actual_return_date = Carbon::today();
        }

        if ($actual === 'RECHAZADO' && $nuevo === 'PENDIENTE') {

            if ($equipment->stock <= 0) {

                return response()->json([
                    'message' => __('messages.no_stock_re_evaluation')
                ], 400);
            }

            $equipment->stock -= 1;
            $equipment->save();
        }

        $loan->status = $nuevo;
        $loan->save();

        return response()->json([
            'message' => __('messages.loan_updated'),
            'data' => $loan
        ]);
    }

    public function destroy(string $id)
    {
        Loan::destroy($id);

        return response()->json([
            'message' => __('messages.loan_deleted')
        ]);
    }

    private function validarTransicion($actual, $nuevo)
    {
        if ($actual === $nuevo) {
            return null;
        }

        switch ($actual) {

            case 'PENDIENTE':
                if (!in_array($nuevo, ['APROBADO', 'RECHAZADO'])) {
                    return __('messages.invalid_transition');
                }
                break;

            case 'APROBADO':
                if (!in_array($nuevo, ['PRESTADO', 'RECHAZADO'])) {
                    return __('messages.invalid_transition');
                }
                break;

            case 'PRESTADO':
                if ($nuevo !== 'DEVUELTO') {
                    return __('messages.invalid_transition');
                }
                break;

            case 'RECHAZADO':
                if ($nuevo !== 'PENDIENTE') {
                    return __('messages.invalid_transition');
                }
                break;

            case 'DEVUELTO':
                return __('messages.returned_loan_locked');
        }

        return null;
    }
}
