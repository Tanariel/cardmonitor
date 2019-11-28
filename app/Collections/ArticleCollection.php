<?php

namespace App\Collections;

use App\Support\Users\CardmarketApi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ArticleCollection extends Collection
{
    public function sync(CardmarketApi $cardmarketApi)
    {
        try {
            $updated_count = 0;
            $cardmarketArticles = $this->toCardmarket();
            $count = count($cardmarketArticles);

            $response = $cardmarketApi->stock->update($cardmarketArticles);

            $notUpdated = $this->setNotUpdated($response['notUpdatedArticles']);

            foreach ($this->items as $key => $article) {
                if (Arr::has($notUpdated, $article->cardmarket_article_id)) {
                    $attributes = [
                        'has_sync_error' => true,
                        'sync_error' => $notUpdated[$article->cardmarket_article_id]['error'],
                    ];
                }
                else {
                    $article->calculateProvision();
                    $attributes = [
                        'cardmarket_article_id' => $response['updatedArticles'][$updated_count]['idArticle'],
                        'has_sync_error' => false,
                        'sync_error' => null,
                    ];
                    $updated_count++;
                }
                $article->update($attributes);
            }
        }
        catch (\Exception $e) {

            dump($cardmarketArticles);
            dump($notUpdated);

            throw $e;
        }
    }

    public function toCardmarket() : array
    {
        $cardmarketArticles = [];
        foreach ($this->items as $key => $article) {
            $cardmarketArticles[$key] = $article->toCardmarket();
        }

        return $cardmarketArticles;
    }

    protected function setNotUpdated($notUpdatedArticles) : array
    {
        $notUpdated = [];

        if (! $notUpdatedArticles) {
            return $notUpdated;
        }

        if (Arr::has($notUpdatedArticles, 'success')) {

            $notUpdatedArticles = [$notUpdatedArticles];

        }

        foreach ($notUpdatedArticles as $key => $notUpdatedArticle) {
            $notUpdated[$notUpdatedArticle['tried']['idArticle']] = [
                'error' => $notUpdatedArticle['error']
            ];
        }

        return $notUpdated;
    }
}