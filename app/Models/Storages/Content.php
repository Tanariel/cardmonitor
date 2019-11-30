<?php

namespace App\Models\Storages;

use App\Models\Articles\Article;
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

    public function assign()
    {
        Article::join('cards', 'cards.id', '=', 'articles.card_id')
            ->whereNull('articles.sold_at')
            ->where('articles.user_id', $this->user_id)
            ->where('cards.expansion_id', $this->storagable_id)
            ->update([
                'storage_id' => $this->storage_id,
            ]);
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
