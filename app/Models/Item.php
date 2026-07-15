<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ObjectModel;

class Item extends Model
{
    /**
     * @property string $id
     * @property string $ownerId
     * @property string|null $parentId
     * @property string|null $objectId
     * @property string $type
     * @property string $nameBlob
     * @property string|null $mimeType
     * @property int $size
     * @property \Illuminate\Support\Carbon|null $deletedAt
     * @property \Illuminate\Support\Carbon|null $createdAt
     * @property \Illuminate\Support\Carbon|null $updatedAt
     * @property-read ObjectModel|null $object
     */
    protected $fillable = [
        'owner_id',
        'parent_id',
        'object_id',
        'type',
        'name_blob',
        'mime_type',
        'size',
    ];

    public function object()
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }
    public function parent()
    {
        return $this->belongsTo(Item::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Item::class, 'parent_id');
    }
}
