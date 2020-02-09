@extends('layouts.app')

@section('content')

    <div class="d-flex mb-3">
        <h2 class="col mb-0"><a class="text-body" href="/order">Bestellung</a><span class="d-none d-md-inline"> > {{ $model->cardmarket_order_id }}<span class="d-none d-lg-inline"> - {{ $model->stateFormatted }}</span></span></h2>
        <div class="d-flex align-items-center">
            <button class="btn btn-secondary ml-1" data-toggle="modal" data-target="#message-create" data-model-id="{{ $model->id }}"><i class="fas fa-envelope"></i></button>
            @if ($model->state == 'paid')
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

    <div class="row align-items-stretch mb-3">

        <div class="col-6 col-sm mb-3 mb-sm-0">
            <div class="card font-weight-bold text-light h-100">
                <div class="card-body {{ (is_null($model->bought_at) ? '' : 'bg-primary') }}">
                    Unbezahlt{{ (is_null($model->bought_at) ? '' : ' : ' . $model->bought_at->format('d.m.Y H:i'))}}
                </div>
            </div>
        </div>

        <div class="col-6 col-sm mb-3 mb-sm-0">
            <div class="card font-weight-bold text-light h-100">
                <div class="card-body {{ (is_null($model->paid_at) ? '' : 'bg-primary') }}">
                    Bezahlt{{ (is_null($model->paid_at) ? '' : ' : ' . $model->paid_at->format('d.m.Y H:i'))}}
                </div>
            </div>
        </div>

        <div class="col-6 col-sm">
            <div class="card font-weight-bold text-light h-100">
                <div class="card-body {{ (is_null($model->sent_at) ? '' : 'bg-primary') }}">
                    Versandt{{ (is_null($model->sent_at) ? '' : ' : ' . $model->sent_at->format('d.m.Y H:i'))}}
                </div>
            </div>
        </div>

        <div class="col-6 col-sm">
            <div class="card font-weight-bold text-light h-100">
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
                        <div class="col-label"><b>Bestellnummer</b></div>
                        <div class="col-value">{{ $model->cardmarket_order_id }}</div>
                    </div>
                    <div class="row">
                        <div class="col-label"><b>Käufer</b></div>
                        <div class="col-value">{{ $model->buyer->username }}</div>
                    </div>
                    <div class="row">
                        <div class="col-label"><b>Verkäufer</b></div>
                        <div class="col-value">{{ $model->seller->username }}</div>
                    </div>
                    <div class="row">
                        <div class="col-label"><b>Karten</b></div>
                        <div class="col-value">{{ $model->articles_count }}</div>
                    </div>
                    @if ($model->evaluation)
                        <div class="row">
                            <div class="col-label">&nbsp;</div>
                            <div class="col-value"></div>
                        </div>
                        <div class="row">
                            <div class="col-label"><b>Allgemeine Bewertung</b></div>
                            <div class="col-value"><evaluation-icon :value="{{ $model->evaluation->grade }}"></evaluation-icon></div>
                        </div>
                        <div class="row">
                            <div class="col-label"><b>Beschreibung der Artikelzustände</b></div>
                            <div class="col-value"><evaluation-icon :value="{{ $model->evaluation->item_description }}"></evaluation-icon></div>
                        </div>
                        <div class="row">
                            <div class="col-label"><b>Verpackung der Bestellung</b></div>
                            <div class="col-value"><evaluation-icon :value="{{ $model->evaluation->packaging }}"></evaluation-icon></div>
                        </div>
                        @if ($model->evaluation->comment)
                            <div class="row">
                                <div class="col-label"><b>Komentar</b></div>
                                <div class="col-value">{{ $model->evaluation->comment }}</div>
                            </div>
                        @endif
                        @if (! empty($model->evaluation->complaint))
                            @foreach ($model->evaluation->complaint as $complaint)
                                <div class="row">
                                    <div class="col-label font-weight-bold text-danger">{{ ($loop->first ? 'Beschwerden' : '') }}</div>
                                    <div class="col-value">{{ $complaint }}</div>
                                </div>
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>

            @if($model->canHaveImages())
                <div class="card mb-3">
                    <div class="card-header d-flex">
                        <div class="col">Bilder</div>
                        <div><a class="text-body" href="{{ $model->path . '/images' }}" title="Bilder"><i class="fas fa-fw fa-images"></i></a></div>
                    </div>
                    <div class="card-body">
                        <imageable-table :model="{{ json_encode($model) }}" token="{{ csrf_token() }}"></imageable-table>
                    </div>
                </div>
            @endif

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
                        <div class="col-label"><b>Umsatz</b></div>
                        <div class="col-value"><b>{{ number_format($model->revenue, 2, ',', '.') }} €</b></div>
                    </div>
                    <div class="row">
                        <div class="col-label">Karten</div>
                        <div class="col-value">{{ number_format($model->articles_revenue, 2, ',', '.') }} €</div>
                    </div>
                    <div class="row">
                        <div class="col-label">Versand</div>
                        <div class="col-value">{{ number_format($model->shipment_revenue, 2, ',', '.') }} €</div>
                    </div>
                    <div class="row">
                        <div class="col-label">&nbsp;</div>
                        <div class="col-value"></div>
                    </div>

                    <div class="row">
                        <div class="col-label"><b>Kosten</b></div>
                        <div class="col-value"><b>{{ number_format($model->cost, 2, ',', '.') }} €</b></div>
                    </div>
                    <div class="row">
                        <div class="col-label">Karten</div>
                        <div class="col-value">{{ number_format($model->articles_cost, 2, ',', '.') }} €</div>
                    </div>
                    <div class="row">
                        <div class="col-label">Provision</div>
                        <div class="col-value">{{ number_format($model->provision, 2, ',', '.') }} €</div>
                    </div>
                    <div class="row">
                        <div class="col-label">Versand</div>
                        <div class="col-value">{{ number_format($model->shipment_cost, 2, ',', '.') }} €</div>
                    </div>
                    <div class="row">
                        <div class="col-label">Sontiges</div>
                        <div class="col-value">{{ number_format($model->items_cost, 2, ',', '.') }} €</div>
                    </div>
                    <div class="row">
                        <div class="col-label">&nbsp;</div>
                        <div class="col-value"></div>
                    </div>

                    <div class="row">
                        <div class="col-label"><b>Gewinn</b></div>
                        <div class="col-value"><b>{{ number_format($model->profit, 2, ',', '.') }} €</b></div>
                    </div>
                    <div class="row">
                        <div class="col-label">Karten</div>
                        <div class="col-value">{{ number_format($model->articles_profit, 2, ',', '.') }} €</div>
                    </div>
                    <div class="row">
                        <div class="col-label">Versand</div>
                        <div class="col-value">{{ number_format($model->shipment_profit, 2, ',', '.') }} €</div>
                    </div>

                </div>
            </div>


        </div>
    </div>

    <order-item-table :model="{{ json_encode($model) }}" :customs="{{ json_encode($customs) }}"></order-item-table>

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
                            <textarea class="form-control" id="message-text" name="message-text" rows="15"></textarea>
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