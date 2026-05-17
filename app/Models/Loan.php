<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'request_date',
        'start_date',
        'estimated_end_date',
        'actual_return_date',
        'justification',
        'status',
        'equipment_id',
        'user_id'
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'start_date' => 'date',
            'estimated_end_date' => 'date',
            'actual_return_date' => 'date',
            'status' => LoanStatus::class
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
