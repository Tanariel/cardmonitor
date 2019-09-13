@extends('layouts.app')

@section('content')

    <div class="d-flex mb-3">
        <h2 class="col"><a class="text-body" href="/order">Bestellung</a> > {{ $model->cardmarket_order_id }} - {{ $model->stateFormatted }}</h2>
        <div class="d-flex align-items-center">
            @if ($model->state == 'paid')
                <button class="btn btn-secondary ml-1" data-toggle="modal" data-target="#message-create" data-model-id="{{ $model->id }}"><i class="fas fa-envelope"></i></button>
                <form action="{{ $model->path . '/send' }}" class="ml-1" method="POST">
                    @csrf

                    <button type="submit" class="btn btn-primary" title="Versenden">Versenden</button>
                </form>
            @endif
            <form action="{{ $model->path . '/sync' }}" class="ml-1" method="POST">
                @csrf
                @method('PUT')

                <button type="submit" class="btn btn-secondary" title="Aktualisieren"><i class="fas fa-fw fa-sync"></i></button>
            </form>
            <a href="{{ url('/order') }}" class="btn btn-secondary ml-1">Übersicht</a>
        </div>
    </div>

    <div class="row mb-3">

        <div class="col">
            <div class="card font-weight-bold text-light">
                <div class="card-body {{ (is_null($model->bought_at) ? '' : 'bg-primary') }}">
                    Unbezahlt{{ (is_null($model->bought_at) ? '' : ' : ' . $model->bought_at->format('d.m.Y H:i'))}}
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card font-weight-bold text-light">
                <div class="card-body {{ (is_null($model->paid_at) ? '' : 'bg-primary') }}">
                    Bezahlt{{ (is_null($model->paid_at) ? '' : ' : ' . $model->paid_at->format('d.m.Y H:i'))}}
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card font-weight-bold text-light">
                <div class="card-body {{ (is_null($model->sent_at) ? '' : 'bg-primary') }}">
                    Versandt{{ (is_null($model->sent_at) ? '' : ' : ' . $model->sent_at->format('d.m.Y H:i'))}}
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card font-weight-bold text-light">
                <div class="card-body {{ (is_null($model->received_at) ? '' : 'bg-primary') }}">
                    Angekommen{{ (is_null($model->received_at) ? '' : ' : ' . $model->received_at->format('d.m.Y H:i'))}}
                </div>
            </div>
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
                            @if ($model->evaluation)
                                <div class="row">
                                    <div class="col-md-4">&nbsp;</div>
                                    <div class="col-md-8"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><b>Allgemeine Bewertung</b></div>
                                    <div class="col-md-8">{{ $model->evaluation->grade }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><b>Beschreibung der Artikelzustände</b></div>
                                    <div class="col-md-8">{{ $model->evaluation->item_description }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><b>Verpackung der Bestellung</b></div>
                                    <div class="col-md-8">{{ $model->evaluation->packaging }}</div>
                                </div>
                                @if ($model->evaluation->comment)
                                    <div class="row">
                                        <div class="col-md-4"><b>Komentar</b></div>
                                        <div class="col-md-8">{{ $model->evaluation->comment }}</div>
                                    </div>
                                @endif
                                @if (! empty($model->evaluation->complaint))
                                    @foreach ($model->evaluation->complaint as $complaint)
                                        <div class="row">
                                            <div class="col-md-4 font-weight-bold text-danger">{{ ($loop->first ? 'Beschwerden' : '') }}</div>
                                            <div class="col-md-8">{{ $complaint }}</div>
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Bilder</div>
                <div class="card-body">
                    <imageable-table :model="{{ json_encode($model) }}" token="{{ csrf_token() }}"></imageable-table>
                </div>
            </div>

        </div>

        <div class="col-md-6">

            <div class="card mb-3">
                <div class="card-header">Versandadresse</div>
                <div class="card-body">
                    {!! nl2br($model->shippingAddressText) !!}
                </div>
            </div>

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
            <order-article-index :model="{{ json_encode($model) }}"></order-article-index>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="message-create">
        <div class="modal-dialog" role="document">
            <form action="/order/{{ $model->id }}/message" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nachricht an {{ $model->buyer->username }} versenden</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="message-text">Nachricht</label>
                            <textarea class="form-control" id="message-text" name="message-text" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Versenden</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection