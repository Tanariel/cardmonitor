<?php

namespace Tests\Unit\Models\Expansions;

use App\Models\Expansions\Expansion;
use App\Models\Localizations\Language;
use App\Models\Localizations\Localization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\RelationshipAssertions;

class ExpansionTest extends TestCase
{
    use RelationshipAssertions;

    /**
     * @test
     */
    public function it_has_many_localizations()
    {
        $model = factory(Expansion::class)->create();

        $this->assertMorphMany($model, Localization::class, 'localizations', 1);
    }

    /**
     * @test
     */
    public function a_default_localization_is_created_after_model_is_created()
    {
        $model = factory(Expansion::class)->create();

        $this->assertCount(1, $model->localizations);

        $this->assertDatabaseHas('localizations', [
            'localizationable_type' => Expansion::class,
            'localizationable_id' => $model->id,
            'language_id' => Language::DEFAULT_ID,
            'name' => $model->name,
        ]);
    }
}
