<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ViewAdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboardAdmin');
    }

    public function catalogo()
    {
        return view('admin.catalogoAdmin');
    }

    public function nuevo()
    {
        return view('admin.agregarItemAdmin', [
            'modo' => 'agregar'
        ]);
    }

    public function editar($id)
    {
        return view('admin.editarItemAdmin', [
            'modo' => 'editar',
            'id' => $id
        ]);
    }

    public function prestamos()
    {
        return view('admin.gestionPrestamosAdmin');
    }

    public function detallePrestamo($id)
    {
        return view('admin.verSolicitudAdmin', [
            'id' => $id
        ]);
    }
}
