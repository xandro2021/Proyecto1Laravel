<?php

namespace App\Enums;

enum LoanStatus: string
{
    case PENDIENTE = 'PENDIENTE';
    case APROBADO = 'APROBADO';
    case RECHAZADO = 'RECHAZADO';
    case PRESTADO = 'PRESTADO';
    case DEVUELTO = 'DEVUELTO';
}
