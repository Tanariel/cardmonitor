<?php

namespace Tests\Unit\Exporters\Orders;

use App\Exporters\Orders\CsvExporter;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Orders\Order;
use Illuminate\Http\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvExporterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_export_an_order()
    {
        Storage::fake('public');

        $path = 'export/' . $this->user->id . '/order/orders.csv';
        Storage::disk('public')->makeDirectory(dirname($path));

        $article = factory(Article::class)->create([
            'user_id' => $this->user->id,
        ]);

        $order = factory(Order::class)->create([
            'user_id' => $this->user->id,
            'state' => 'paid',
        ]);

        $this->assertCount(0, $order->articles);

        $order->articles()->attach($article->id);

        $this->assertCount(1, $order->fresh()->articles);

        $order->load([
            'articles.language',
            'articles.card.expansion',
            'buyer',
        ]);

        $orders = new Collection();
        $orders->push($order);

        Storage::disk('public')->assertMissing($path);

        $url = CsvExporter::all($this->user->id, $orders, $path);

        $this->assertEquals(Storage::disk('public')->url($path), $url);

        Storage::disk('public')->assertExists($path);

        $file = new \SplFileObject(Storage::disk('public')->path($path));
        $file->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $rows = [];
        while (! $file->eof()) {
            $rows[] = $file->fgetcsv(';');
        }

        $this->assertCount(4, $rows);
        $this->assertEquals(array_merge(CsvExporter::BUYER_ATTRIBUTES, CsvExporter::ORDER_ATTRIBUTES, CsvExporter::ARTICLE_ATTRIBUTES), $rows[0]);
        $this->assertEquals('Artikel', $rows[1][30]);
        $this->assertEquals('Versandposition', $rows[2][30]);

        Storage::disk('public')->delete($path);

        Storage::disk('public')->assertMissing($path);
    }

    /**
     * @test
     */
    public function it_can_group_articles()
    {
        $this->markTestIncomplete('TODO');
    }
}
