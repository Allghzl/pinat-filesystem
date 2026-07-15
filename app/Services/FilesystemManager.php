<?php

namespace App\Services;

use App\Services\StorageService;
use App\Services\ObjectService;
use App\Services\ItemService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FilesystemManager
{
    public function __construct(
        protected StorageService $storageService,
        protected ObjectService $objectService,
        protected ItemService $itemService,
    ) {}

    public function uploadFile(
        string $ownerId,
        ?string $parentId,
        UploadedFile $file,
        string $bucket = 'drive'
    ) {
        $objectId = (string) Str::uuid7();
        $objectKey = $ownerId . '/' . $objectId;

        DB::transaction(function () use (
            $ownerId,
            $parentId,
            $file,
            $bucket,
            $objectId,
            $objectKey
        ) {
            $this->storageService->putFile($objectKey, $file, $bucket);

            $object = $this->objectService->createObject(
                bucket: $bucket,
                objectKey: $objectKey,
                size: $file->getSize() ?? 0,
                checksum: null,
            );

            $this->objectService->markReady($object->id);

            $this->itemService->createFile(
                ownerId: $ownerId,
                parentId: $parentId,
                objectId: $object->id,
                nameBlob: $file->getClientOriginalName(),
                mimeType: $file->getMimeType(),
                size: $file->getSize() ?? 0,
            );
        });

        return true;
    }
}
