<?php

use App\Enums\EquipmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type');

            $table->text('description')->nullable();

            $table->integer('stock');

            $table->enum('status', [
                EquipmentStatus::DISPONIBLE->value,
                EquipmentStatus::OCUPADO->value,
                EquipmentStatus::MANTENIMIENTO->value
            ]);

            $table->string('image_filename')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
