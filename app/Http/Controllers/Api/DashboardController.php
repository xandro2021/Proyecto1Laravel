<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Loan;

class DashboardController extends Controller
{
    public function stats()
    {
        $equiposDisponibles = Equipment::where('status', 'DISPONIBLE')->count();

        $prestamosActivos = Loan::where('status', 'APROBADO')->count();

        $solicitudesPendientes = Loan::where('status', 'PENDIENTE')->count();

        $prestamosDevueltos = Loan::where('status', 'DEVUELTO')->count();

        $totalEquipos = Equipment::count();

        return response()->json([
            'equiposDisponibles' => $equiposDisponibles,
            'prestamosActivos' => $prestamosActivos,
            'solicitudesPendientes' => $solicitudesPendientes,
            'prestamosDevueltos' => $prestamosDevueltos,
            'totalEquipos' => $totalEquipos
        ]);
    }

    public function topEquipos()
    {
        $top = Loan::selectRaw('equipment_id, COUNT(*) as prestamos')
            ->with('equipment:id,name')
            ->groupBy('equipment_id')
            ->orderByDesc('prestamos')
            ->limit(3)
            ->get();

        $resultado = $top->map(function ($item) {
            return [
                'nombre' => $item->equipment?->name ?? 'Sin nombre',
                'prestamos' => $item->prestamos
            ];
        });

        return response()->json($resultado);
    }
}
