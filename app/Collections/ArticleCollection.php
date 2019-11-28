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
            $updatesArticleCount = 0;
            $cardmarketArticles = $this->toCardmarket();
            $count = count($cardmarketArticles);

            $response = $cardmarketApi->stock->update($cardmarketArticles);

            $notUpdated = $this->setNotUpdated($response['notUpdatedArticles']);
            $updated = $response['updatedArticles'];

            foreach ($this->items as $key => $article) {
                if (Arr::has($notUpdated, $article->cardmarket_article_id)) {
                    $attributes = [
                        'has_sync_error' => true,
                        'sync_error' => $notUpdated[$article->cardmarket_article_id]['error'],
                    ];
                }
                else {
                    $article->calculateProvision();
                    if ($updatesArticleCount == 0) {
                        $updatesArticleCount = $updated[$updated_count]['count'];
                    }
                    $attributes = [
                        'cardmarket_article_id' => $updated[$updated_count]['idArticle'],
                        'has_sync_error' => false,
                        'sync_error' => null,
                    ];
                    $updatesArticleCount--;
                    if ($updatesArticleCount == 0) {
                        $updated_count++;
                    }
                }
                $article->update($attributes);
            }
        }
        catch (\Exception $e) {

            dump('cardmarketArticles', $cardmarketArticles);
            dump('updated_count', $updated_count);
            dump('updated', $updated);
            dump('notUpdated', $notUpdated);
            dump('response', $response);

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