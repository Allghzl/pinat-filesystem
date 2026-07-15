<?php

namespace App\Services;

use App\Models\ObjectModel;
use Illuminate\Support\Str;

class ObjectService
{
    public function createObject(
        string $bucket,
        string $objectKey,
        int $size,
        ?string $checksum
    ): ObjectModel {
        return ObjectModel::create([
            'id'         => (string) Str::uuid7(),
            'bucket'     => $bucket,
            'object_key' => $objectKey,
            'size'       => $size,
            'checksum'   => $checksum,
            'status'     => 'uploading',
            'uploaded_at' => null,
        ]);
    }

    public function find(string $id): ObjectModel
    {
        return ObjectModel::query()->findOrFail($id);
    }

    public function markReady(string $id): ObjectModel
    {
        $object = $this->find($id);

        $object->update([
            'status' => 'ready',
            'uploaded_at' => now(),
        ]);

        return $object->refresh();
    }

    public function markFailed(string $id): ObjectModel
    {
        $object = $this->find($id);

        $object->update([
            'status' => 'failed',
        ]);

        return $object->refresh();
    }

    public function delete(string $id): bool
    {
        return (bool) ObjectModel::query()->whereKey($id)->delete();
    }
}
