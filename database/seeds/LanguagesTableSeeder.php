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

        $languages = [
            2 => 'French',
            3 => 'German',
            4 => 'Spanish',
            5 => 'Italian',
        ];
        foreach ($languages as $id => $name) {
            Language::create([
                'id' => $id,
                'name' => $name,
            ]);
        }
    }
}
