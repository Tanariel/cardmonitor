<?php

namespace Tests\Feature\Commands\Setup;

use App\Models\Apis\Api;
use App\Models\Localizations\Language;
use Cardmonitor\Cardmarket\Api as CardmarketApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DevCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_sets_up_the_dev_environment()
    {
        $this->markTestSkipped();

        $api = factory(Api::class)->create([
            'user_id' => $this->user->id,
            'accessdata' => [
                'app_token' => '8Ts9QDnOCD7gukTV',
                'app_secret' => 'Zy7x2e1gkVcCQat50qd8XtsyMA9qatRN',
                'access_token' => 'LMDxSPkFfCBIYTULl3yHdswrwbYCZEzf',
                'access_token_secret' => 'PgHYR3j8o0Itktu47AbkRRE1foccd91r',
                'url' => CardmarketApi::URL_SANDBOX,
            ],
        ]);

        $this->artisan('setup:dev');
    }
}
