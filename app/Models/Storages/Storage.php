<?php

namespace App\Models\Storages;

use App\Models\Articles\Article;
use App\Models\Storages\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Storage extends Model
{
    protected $appends = [
        'editPath',
        'isDeletable',
        'path',
    ];

    protected $guarded = [];

    /**
     * The booting method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            if (! $model->user_id) {
                $model->user_id = auth()->user()->id;
            }

            $model->setFullName();

            return true;
        });

        static::updating(function($model)
        {
            $model->setFullName();

            return true;
        });
    }

    public function isDeletable() : bool
    {
        return (! $this->articles()->exists());
    }

    public function getPathAttribute()
    {
        return $this->path('show');
    }

    public function getEditPathAttribute()
    {
        return $this->path('edit');
    }

    protected function path(string $action = '') : string
    {
        return route($this->baseRoute() . '.' . $action, ['storage' => $this->id]);
    }

    protected function baseRoute() : string
    {
        return 'storage';
    }

    public function getIsDeletableAttribute()
    {
        return $this->isDeletable();
    }

    public function setFullName()
    {
        $this->attributes['full_name'] = $this->attributes['name'];
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function contents() : HasMany
    {
        return $this->hasMany(Content::class);
    }

}
