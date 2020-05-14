<?php

namespace App\APIs\Skryfall;

use App\Apis\ApiModel;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Expansion extends ApiModel
{
    public $timestamps = false;

    public static function all()
    {

        return Cache::remember('skryfall.sets', 3600, function () {
            $api = App::make('SkryfallApi');
            $data = $api->set->all();
            return $data['data'];
        });
    }

    public static function find(int $id)
    {

    }

    public static function findByCode(string $code)
    {
        $api = App::make('SkryfallApi');

        try {
            $attributes = $api->set->findByCode($code);

            $model = new self();
            $model->fill($attributes);

            return $model;
        }
        catch(ClientException $e) {
            return null;
        }

    }

    public function cards() : Collection
    {
        return Card::fromSet($this->code);
    }

}
?>