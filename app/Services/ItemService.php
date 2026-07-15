<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemService
{
    public function createFolder(
        string $ownerId,
        ?string $parentId,
        string $nameBlob
    ): Item {
        if ($parentId) {
            $parent = Item::query()
                ->where('id', $parentId)
                ->where('owner_id', $ownerId)
                ->where('type', 'folder')
                ->first();

            if (!$parent) {
                throw new NotFoundHttpException('Parent folder not found or does not belong to the owner.');
            }
        }

        return Item::create([
            'id'         => (string) Str::uuid7(),
            'owner_id'   => $ownerId,
            'parent_id'  => $parentId,
            'object_id'  => null,
            'type'       => 'folder',
            'name_blob'  => $nameBlob,
            'mime_type'  => null,
            'size'       => 0,
            'checksum'   => null,
        ]);
    }

    public function createFile(
        string $ownerId,
        ?string $parentId,
        ?string $objectId,
        string $nameBlob,
        string $mimeType,
        int $size,
    ): Item {
        if ($parentId) {
            $parent = Item::query()
                ->where('id', $parentId)
                ->where('owner_id', $ownerId)
                ->where('type', 'folder')
                ->first();

            if (!$parent) {
                throw new NotFoundHttpException('Parent folder not found or does not belong to the owner.');
            }
        }

        return Item::create([
            'id'         => (string) Str::uuid7(),
            'owner_id'   => $ownerId,
            'parent_id'  => $parentId,
            'object_id'  => $objectId,
            'type'       => 'file',
            'name_blob'  => $nameBlob,
            'mime_type'  => $mimeType,
            'size'       => $size,
        ]);
    }

    public function renameItem(Item $item, string $newNameBlob): Item
    {
        $item->name_blob = $newNameBlob;
        $item->save();

        return $item;
    }

    public function moveItem(Item $item, ?string $newParentId): Item
    {
        if ($newParentId) {
            $newParent = Item::query()
                ->where('id', $newParentId)
                ->where('owner_id', $item->owner_id)
                ->where('type', 'folder')
                ->first();

            if (!$newParent) {
                throw new NotFoundHttpException('New parent folder not found or does not belong to the owner.');
            }
        }

        $item->parent_id = $newParentId;
        $item->save();

        return $item;
    }

    public function deleteItem(Item $item): void
    {
        $item->delete();
    }

    public function getItemById(string $itemId, string $ownerId): ?Item
    {
        return Item::query()
            ->where('id', $itemId)
            ->where('owner_id', $ownerId)
            ->first();
    }

    public function getItemsByParentId(?string $parentId, string $ownerId)
    {
        return Item::query()
            ->where('parent_id', $parentId)
            ->where('owner_id', $ownerId)
            ->get();
    }
}
