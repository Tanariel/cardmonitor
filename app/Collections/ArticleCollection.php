<?php

namespace App\Collections;

use App\Support\Users\CardmarketApi;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ArticleCollection extends Collection
{
    public function sync(CardmarketApi $cardmarketApi)
    {
        $cardmarketArticles = [];
        $notUpdated = [];
        $count = 0;
        $updated_count = 0;

        foreach ($this->items as $key => $article) {
            $cardmarketArticles[$key] = $article->toCardmarket();
            $count++;
        }

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