<?php

namespace App\Enums;

enum EquipmentStatus: string
{
    case DISPONIBLE = 'DISPONIBLE';
    case OCUPADO = 'OCUPADO';
    case MANTENIMIENTO = 'MANTENIMIENTO';
}
