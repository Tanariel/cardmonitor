<?php

namespace Tests\Feature\Controller\Cardmarket\Articles;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleControllerTest extends TestCase
{
    /**
     * @test
     */
    public function a_user_can_sync_all_articles()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $response = $this->put(route('article.sync.update'));
    }
}
