<?php

namespace Tests\Unit\Support;

use App\Support\Locale;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_a_list_of_locales()
    {
        $data = Locale::list();
        $this->assertInstanceOf(Collection::class, $data);
    }

    /**
     * @test
     */
    public function it_gets_the_name_of_a_language()
    {
        $this->assertEquals('Deutsch', Locale::name('de'));
        $this->assertEquals('Englisch', Locale::name('en'));
    }

    /**
     * @test
     */
    public function it_gets_the_originale_name_of_a_language()
    {
        $this->assertEquals('Deutsch', Locale::name('de', 'de'));
        $this->assertEquals('English', Locale::name('en', 'en'));
    }
}
