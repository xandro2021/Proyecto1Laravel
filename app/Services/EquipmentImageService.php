<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EquipmentImageService
{
    private string $uploadDir = 'uploads/equipment';

    /**
     * Guarda imagen y retorna filename
     */
    public function saveImage(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        // validar mime
        if (!str_starts_with($file->getMimeType(), 'image/')) {
            throw new \Exception('El archivo debe ser una imagen');
        }

        // crear carpeta si no existe
        if (!File::exists(public_path($this->uploadDir))) {
            File::makeDirectory(
                public_path($this->uploadDir),
                0755,
                true
            );
        }

        // nombre único
        $filename =
            Str::uuid()
            . '_'
            . time()
            . '.'
            . $file->getClientOriginalExtension();

        // mover archivo
        $file->move(
            public_path($this->uploadDir),
            $filename
        );

        return $filename;
    }

    /**
     * Eliminar imagen
     */
    public function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $path = public_path(
            $this->uploadDir . '/' . $filename
        );

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
