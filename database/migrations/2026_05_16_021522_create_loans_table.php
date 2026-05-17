<?php

use App\Enums\LoanStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->date('request_date');

            $table->date('start_date')->nullable();

            $table->date('estimated_end_date');

            $table->date('actual_return_date')->nullable();

            $table->text('justification')->nullable();

            $table->enum('status', [
                LoanStatus::PENDIENTE->value,
                LoanStatus::APROBADO->value,
                LoanStatus::RECHAZADO->value,
                LoanStatus::PRESTADO->value,
                LoanStatus::DEVUELTO->value
            ]);

            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
