<?php

namespace App\Console\Commands\Storage;

use App\Models\Expansions\Expansion;
use App\Models\Storages\Content;
use App\Models\Storages\Storage;
use App\User;
use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up my storages';

    protected $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->user = User::find(1);

        Content::where('user_id', $this->user->id)->delete();
        Storage::where('user_id', $this->user->id)->delete();

        $regale = [];
        $regale[1] = Storage::create([
            'name' => 'Regal 1',
            'user_id' => $this->user->id,
        ]);

        $regale[2] = Storage::create([
            'name' => 'Regal 2',
            'user_id' => $this->user->id,
        ]);

        $regale[3] = Storage::create([
            'name' => 'Regal 3',
            'user_id' => $this->user->id,
        ]);

        $regale[4] = Storage::create([
            'name' => 'Regal 4',
            'user_id' => $this->user->id,
        ]);

        $faecher = [];
        foreach ($regale as $regalNr => $regal) {
            for ($kartonNr = 1; $kartonNr <= 3; $kartonNr++) {
                $karton = Storage::create([
                    'name' => 'Karton ' . $kartonNr,
                    'user_id' => $this->user->id,
                    'parent_id' => $regal->id,
                ]);

                for ($fachNr = 1; $fachNr <= 4; $fachNr++) {
                    $fach = Storage::create([
                        'name' => 'Fach ' . $fachNr,
                        'user_id' => $this->user->id,
                        'parent_id' => $karton->id,
                    ]);
                    $faecher[$regalNr][$kartonNr][$fachNr] = $fach;
                }
            }
        }

        $faecher[1][1][1]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 2,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 6,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 10,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 76,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 23,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 29,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 37,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][1][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 44,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 49,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 74,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][1][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1652,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 109,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1197,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1280,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1388,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1449,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][1][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1485,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1835,
                'user_id' => $this->user->id,
            ],
        ]);

        // $faecher[1][2][1]

        $faecher[1][2][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 4,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 5,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 7,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 8,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 9,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 14,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 11,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][2][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 15,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 52,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 16,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 17,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 18,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 19,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 20,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 21,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][2][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 26,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 27,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 28,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 32,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 33,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 34,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 35,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][3][1]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 36,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 38,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 39,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 40,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][3][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 41,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 42,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 43,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][3][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 45,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[1][3][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 46,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 47,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][1][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 31,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][1][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 48,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 51,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][1][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 50,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 55,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][2][1]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 53,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 54,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][2][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 56,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 58,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 70,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 84,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][2][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 92,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 95,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 99,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 102,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 106,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 108,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][2][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 114,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 120,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 118,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1206,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1253,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][3][1]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1262,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1327,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1345,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1358,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1389,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1424,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1435,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1457,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][3][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1469,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1481,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][3][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1495,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[2][3][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1522,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1601,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1668,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1676,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1694,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][1][1]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1717,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][1][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1695,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1729,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][1][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1731,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1812,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1829,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][1][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1822,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 2348,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 69,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 113,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1194,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1273,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1369,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1464,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1718,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1418,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][2][1]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1513,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1679,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1483,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1719,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1813,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 2110,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 2448,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1730,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1830,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1722,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][2][2]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 2111,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1702,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][2][3]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 12,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 64,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 62,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 67,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1444,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1641,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1696,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1727,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 60,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 96,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1811,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1820,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 2397,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 25,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 24,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 91,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 104,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 107,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 115,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 119,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1203,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1261,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1288,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1350,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1398,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1430,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1456,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1477,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1496,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1663,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1692,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1720,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1262,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1728,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1593,
                'user_id' => $this->user->id,
            ],
        ]);

        $faecher[4][2][4]->contents()->createMany([
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1484,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 22,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 59,
                'user_id' => $this->user->id,
            ],
            [
                'storagable_type' => Expansion::class,
                'storagable_id' => 1821,
                'user_id' => $this->user->id,
            ],
        ]);
    }
}
