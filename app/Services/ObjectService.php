<?php

namespace App\Services;

use App\Models\ObjectModel;
use Illuminate\Support\Str;

class ObjectService
{
    private function makeObjectKey(string $ownerId, string $objectId): string
    {
        return "{$ownerId}/{$objectId}";
    }
    public function createObject(
        string $bucket,
        string $ownerId,
        int $size,
        ?string $checksum
    ): ObjectModel {
        $objectId = (string) Str::uuid7();
        return ObjectModel::create([
            'id'         => $objectId,
            'bucket'     => $bucket,
            'object_key' => $this->makeObjectKey($ownerId, $objectId),
            'size'       => $size,
            'checksum'   => $checksum,
            'status'     => 'uploading',
            'uploaded_at' => null,
        ]);
    }

    public function findById(string $id): ObjectModel
    {
        return ObjectModel::query()->findOrFail($id);
    }

    public function findByObjectKey(string $bucket, string $objectKey): ?ObjectModel
    {
        return ObjectModel::query()
            ->where('bucket', $bucket)
            ->where('object_key', $objectKey)
            ->first();
    }

    public function findByChecksum(string $bucket, string $checksum): ?ObjectModel
    {
        return ObjectModel::query()
            ->where('bucket', $bucket)
            ->where('checksum', $checksum)
            ->first();
    }

    public function findReady(string $bucket, string $objectKey): ?ObjectModel
    {
        return ObjectModel::query()
            ->where('bucket', $bucket)
            ->where('object_key', $objectKey)
            ->where('status', 'ready')
            ->first();
    }

    public function markReady(string $objectId): ObjectModel
    {
        $object = $this->findById($objectId);

        $object->update([
            'status' => 'ready',
            'uploaded_at' => now(),
        ]);

        return $object->refresh();
    }

    public function markFailed(string $objectId): ObjectModel
    {
        $object = $this->findById($objectId);

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
