<?php

use App\Models\Localizations\Language;
use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Language::class)->create([
            'id' => Language::DEFAULT_ID,
            'name' => Language::DEFAULT_NAME,
        ]);
    }
}
