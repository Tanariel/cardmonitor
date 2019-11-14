<?php

namespace Tests\Unit\Models\Storages;

use App\Models\Expansions\Expansion;
use App\Models\Storages\Content;
use App\Models\Storages\Storage;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContentTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_a_storage_id_for_an_expansion()
    {
        $expansionWithContent = factory(Expansion::class)->create();
        $expansionWithoutContent = factory(Expansion::class)->create();
        $user = factory(User::class)->create();
        $storage = factory(Storage::class)->create([
            'user_id' => $user->id,
        ]);
        $content = factory(Content::class)->create([
            'user_id' => $user->id,
            'storage_id' => $storage->id,
            'storagable_type' => Expansion::class,
            'storagable_id' => $expansionWithContent->id,
        ]);

        $this->assertEquals(0, Content::findStorageIdByExpansion($user->id, $expansionWithoutContent->id)->storage_id);
        $this->assertEquals($storage->id, Content::findStorageIdByExpansion($user->id, $expansionWithContent->id)->storage_id);
    }
}
