<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class ViewUserController extends Controller
{
    public function catalogo()
    {
        return view('usuario.catalogoUser');
    }

    public function solicitud($id)
    {
        return view('usuario.solicitudPrestamoUser', [
            'id' => $id
        ]);
    }

    public function prestamos()
    {
        return view('usuario.historialSolicitudesUser');
    }

    public function detalle($id)
    {
        return view('usuario.verSolicitudUser', [
            'id' => $id
        ]);
    }
}
