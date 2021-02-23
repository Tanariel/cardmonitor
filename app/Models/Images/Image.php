<?php

namespace App\Models\Images;

use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    const MIME_TYPES = [
        'jpeg',
        'png',
    ];

    protected $appends = [
        'url',
    ];

    protected $guarded = [
        'id',
        'url',
    ];

    public static function createFromUploadedFile(UploadedFile $file, Order $order) : self
    {
        $image = $order->images()->create([
            'user_id' => $order->user_id,
            'mime' => $file->getClientMimeType(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'extension' => $file->extension(),
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'filename' => Str::uuid() . '.' . $file->extension(),
        ]);

        $path = $file->storeAs('images', $image->filename, ['disk' => 'public']);

        return $image;
    }

    public function isDeletable() : bool
    {
        return true;
    }

    public function getUrlAttribute() : string
    {
        return Storage::url('images/' . $this->filename);
    }

    public function imageable() : MorphTo
    {
        return $this->morphTo('imageable');
    }
}
