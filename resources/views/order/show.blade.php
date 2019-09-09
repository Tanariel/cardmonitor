@extends('layouts.app')

@section('content')

    <div class="d-flex mb-3">
        <h2 class="col"><a class="text-body" href="/order">Bestellung</a> > {{ $model->cardmarket_order_id }} - {{ $model->stateFormatted }}</h2>
        <div class="d-flex align-items-center">
            <a href="{{ url('/item') }}" class="btn btn-secondary ml-1">Übersicht</a>
        </div>
    </div>
    <div class="row">

        <div class="col-md-6">

            <div class="card mb-3">
                <div class="card-header">{{ $model->cardmarket_order_id }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-4"><b>Bestellnummer</b></div>
                                <div class="col-md-8">{{ $model->cardmarket_order_id }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><b>Käufer</b></div>
                                <div class="col-md-8">{{ $model->buyer->username }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><b>Verkäufer</b></div>
                                <div class="col-md-8">{{ $model->seller->username }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><b>Karten</b></div>
                                <div class="col-md-8">{{ $model->articles_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Versandadresse</div>
                <div class="card-body">
                    {!! nl2br($model->shippingAddressText) !!}
                </div>
            </div>

        </div>

        <div class="col-md-6">

            <div class="card mb-3">
                <div class="card-header">Kalkulation</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-4"><b>Umsatz</b></div>
                                <div class="col-md-8"><b>{{ number_format($model->revenue, 2, ',', '.') }} €</b></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Karten</div>
                                <div class="col-md-8">{{ number_format($model->articles_revenue, 2, ',', '.') }} €</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Versand</div>
                                <div class="col-md-8">{{ number_format($model->shipment_revenue, 2, ',', '.') }} €</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">&nbsp;</div>
                                <div class="col-md-8"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Kosten</b></div>
                                <div class="col-md-8"><b>{{ number_format($model->cost, 2, ',', '.') }} €</b></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Karten</div>
                                <div class="col-md-8">{{ number_format($model->articles_cost, 2, ',', '.') }} €</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Provision</div>
                                <div class="col-md-8">{{ number_format($model->provision, 2, ',', '.') }} €</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Versand</div>
                                <div class="col-md-8">{{ number_format($model->shipment_cost, 2, ',', '.') }} €</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Sontiges</div>
                                <div class="col-md-8">{{ number_format($model->items_cost, 2, ',', '.') }} €</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">&nbsp;</div>
                                <div class="col-md-8"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Gewinn</b></div>
                                <div class="col-md-8"><b>{{ number_format($model->profit, 2, ',', '.') }} €</b></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Karten</div>
                                <div class="col-md-8">{{ number_format($model->articles_profit, 2, ',', '.') }} €</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">Versand</div>
                                <div class="col-md-8">{{ number_format($model->shipment_profit, 2, ',', '.') }} €</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            @if (count($model->sales) > 0)
                <div class="card mb-3">
                    <div class="card-header">Kosten</div>
                    <div class="card-body">
                        @foreach($model->sales as $sale)
                            <div class="row">
                                <div class="col-md-4"><b>{{ $sale->item->name }}</b></div>
                                <div class="col-md-4">{{ number_format($sale->quantity, 2, ',', '.') }} Stück</div>
                                <div class="col-md-4">{{ number_format(($sale->quantity * $sale->unit_cost), 2, ',', '.') }} €</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    <div class="card">
        <div class="card-header">Artikel</div>
        <div class="card-body">
            <order-article-table :model="{{ json_encode($model) }}"></order-article-table>
        </div>
    </div>


@endsection