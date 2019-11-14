<?php

namespace App\Models\Storages;

use App\Models\Expansions\Expansion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

class Content extends Model
{
    protected $appends = [
        'isDeletable',
    ];

    protected $guarded = [];

    protected $table = 'content_storage';

    protected static $defaultStorages = [];

    public static function findStorageIdByExpansion(int $userId, int $expansionId) : self
    {
        $content = self::latest()
            ->firstOrNew([
                'user_id' => $userId,
                'storagable_type' => Expansion::class,
                'storagable_id' => $expansionId
            ], [
                'storage_id' => 0,
            ]);

        return $content;
    }

    public static function defaultStorage(int $userId, int $expansionId)
    {
        if (Arr::has(self::$defaultStorages, $expansionId)) {
            return self::$defaultStorages[$expansionId];
        }

        $storageId = self::findStorageIdByExpansion($userId, $expansionId)->storage_id;
        self::$defaultStorages[$expansionId] = $storageId ?: null;

        return self::$defaultStorages[$expansionId];
    }

    public function getIsDeletableAttribute() : bool
    {
        return $this->isDeletable();
    }

    public function isDeletable() : bool
    {
        return true;
    }

    public function storagable() : MorphTo
    {
        return $this->morphTo('storagable');
    }

}
