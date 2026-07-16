<?php

namespace App\Services;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;

class StorageService
{
    protected S3Client $client;

    public function __construct()
    {
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'endpoint' => config('filesystems.disks.s3.endpoint'),
            'use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);
    }

    /**
     * Upload file.
     */
    public function upload(
        string $bucket,
        string $objectKey,
        UploadedFile $file,
    ): string {

        $this->client->putObject([
            'Bucket' => $bucket,
            'Key' => $objectKey,
            'Body' => fopen($file->getRealPath(), 'rb'),
            'ContentType' => $file->getMimeType(),
        ]);

        return $objectKey;
    }

    /**
     * Upload raw content.
     */
    public function put(
        string $bucket,
        string $objectKey,
        string $content,
        ?string $contentType = null,
    ): string {

        $params = [
            'Bucket' => $bucket,
            'Key' => $objectKey,
            'Body' => $content,
        ];

        if ($contentType) {
            $params['ContentType'] = $contentType;
        }

        $this->client->putObject($params);

        return $objectKey;
    }

    /**
     * Download object.
     */
    public function get(
        string $bucket,
        string $objectKey,
    ): string {

        $result = $this->client->getObject([
            'Bucket' => $bucket,
            'Key' => $objectKey,
        ]);

        return (string) $result['Body'];
    }

    /**
     * Read stream.
     */
    public function readStream(
        string $bucket,
        string $objectKey,
    ) {

        $result = $this->client->getObject([
            'Bucket' => $bucket,
            'Key' => $objectKey,
        ]);

        return $result['Body']->detach();
    }

    /**
     * Check object exists.
     */
    public function exists(
        string $bucket,
        string $objectKey,
    ): bool {

        try {

            $this->client->headObject([
                'Bucket' => $bucket,
                'Key' => $objectKey,
            ]);

            return true;
        } catch (AwsException) {

            return false;
        }
    }

    /**
     * Delete object.
     */
    public function delete(
        string $bucket,
        string $objectKey,
    ): bool {

        $this->client->deleteObject([
            'Bucket' => $bucket,
            'Key' => $objectKey,
        ]);

        return true;
    }

    /**
     * Copy object.
     */
    public function copy(
        string $sourceBucket,
        string $sourceKey,
        string $destinationBucket,
        string $destinationKey,
    ): void {

        $this->client->copyObject([
            'Bucket' => $destinationBucket,
            'Key' => $destinationKey,
            'CopySource' => "{$sourceBucket}/{$sourceKey}",
        ]);
    }

    /**
     * Move object.
     */
    public function move(
        string $sourceBucket,
        string $sourceKey,
        string $destinationBucket,
        string $destinationKey,
    ): void {

        $this->copy(
            $sourceBucket,
            $sourceKey,
            $destinationBucket,
            $destinationKey,
        );

        $this->delete(
            $sourceBucket,
            $sourceKey,
        );
    }

    /**
     * Generate temporary URL.
     */
    public function temporaryUrl(
        string $bucket,
        string $objectKey,
        int $minutes = 10,
    ): string {

        $command = $this->client->getCommand(
            'GetObject',
            [
                'Bucket' => $bucket,
                'Key' => $objectKey,
            ]
        );

        $request = $this->client->createPresignedRequest(
            $command,
            "+{$minutes} minutes",
        );

        return (string) $request->getUri();
    }
}
