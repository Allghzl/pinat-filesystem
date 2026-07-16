    <?php

    namespace App\Services;

    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\DB;
    use Throwable;

    class FilesystemService
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
        ): array {

            /**
             * 1. Create object metadata
             */
            $object = $this->objectService->createObject(
                bucket: $bucket,
                ownerId: $ownerId,
                size: $file->getSize() ?? 0,
                checksum: null,
            );

            try {

                /**
                 * 2. Upload blob ke MinIO
                 */
                $this->storageService->putFile(
                    objectKey: $object->object_key,
                    file: $file,
                    bucket: $bucket,
                );
            } catch (Throwable $e) {

                $this->objectService->markFailed(
                    $object->id
                );

                report($e);

                throw $e;
            }

            /**
             * 3. Finalize upload
             */
            return DB::transaction(function () use (
                $object,
                $ownerId,
                $parentId,
                $file,
            ) {

                $object = $this->objectService->markReady(
                    $object->id
                );

                $item = $this->itemService->createFile(
                    ownerId: $ownerId,
                    parentId: $parentId,
                    objectId: $object->id,
                    nameBlob: $file->getClientOriginalName(),
                    mimeType: $file->getMimeType(),
                    size: $file->getSize() ?? 0,
                );

                return [
                    'item' => $item,
                    'object' => $object,
                ];
            });
        }
    }
