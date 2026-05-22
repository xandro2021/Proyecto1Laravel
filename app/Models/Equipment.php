<?php

namespace App\Models;

use App\Enums\EquipmentStatus;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';
    protected $appends = ['image_url'];

    protected $hidden = [];


    protected $fillable = [
        'name',
        'type',
        'description',
        'stock',
        'status',
        'image_filename'
    ];

    protected function casts(): array
    {
        return [
            'status' => EquipmentStatus::class
        ];
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image_filename
            ? asset('uploads/equipment/' . $this->image_filename)
            : asset('img/no-image.png');
    }
}
