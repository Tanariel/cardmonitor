<?php

namespace App\Transformers\Articles\Csvs;

class Yugioh
{
    public static function transform(array $data) : array
    {
        return [
            'card_id' => $data[1],
            'language_id' => $data[7],
            'cardmarket_article_id' => $data[0],
            'condition' => $data[8],
            'unit_price' => $data[6],
            'sold_at' => null,
            'is_in_shoppingcard' => false,
            'is_foil' => false,
            'is_signed' => ($data[9] == 'X' ? true : false),
            'is_altered' => ($data[11] == 'X' ? true : false),
            'is_playset' => false,
            'cardmarket_comments' => $data[12],
            'has_sync_error' => false,
            'sync_error' => null,
        ];
    }
}