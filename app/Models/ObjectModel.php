<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectModel extends Model
{
    protected $table = 'objects';

    protected $fillable = [
        'bucket',
        'object_key',
        'size',
        'checksum',
        'status',
        'uploaded_at',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function item()
    {
        return $this->hasOne(Item::class, 'object_id', 'id');
    }
}
