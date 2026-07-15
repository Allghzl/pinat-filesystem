<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StorageService
{
    public function put(string $path, string $content, string $disk = 's3'): string
    {
        Storage::disk($disk)->put($path, $content);

        return $path;
    }

    public function putFile(string $path, UploadedFile $file, string $disk = 's3'): string
    {
        Storage::disk($disk)->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        return $path;
    }

    public function get(string $path, string $disk = 's3'): string
    {
        return Storage::disk($disk)->get($path);
    }

    public function exists(string $path, string $disk = 's3'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    public function delete(string $path, string $disk = 's3'): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    public function temporaryUrl(string $path, int $minutes = 10, string $disk = 's3'): string
    {
        return Storage::disk($disk)->temporaryUrl(
            $path,
            now()->addMinutes($minutes)
        );
    }
}
