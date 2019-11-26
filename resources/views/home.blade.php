@extends('layouts.app')

@section('content')
    <div class="row mb-3 align-items-stretch">

        @if ($cardmarketConnectLink)
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">Cardmarket Konto</div>
                    <div class="card-body">
                        @if ($cardmarketConnectLink)
                            <a href="{{ $cardmarketConnectLink }}">Verbinde dein Cardmarket Konto mit Cardmonitor</a>
                        @else
                            <div>{{ $cardmarketAccount['username'] }} {{ $cardmarketAccount['reputation'] }}</div>
                            <div>{{ number_format($cardmarketAccount['moneyDetails']['totalBalance'], 2, ',', '.') }} €</div>
                            @if ($cardmarketAccount['unreadMessages'])
                                <div>{{ $cardmarketAccount['unreadMessages'] }} ungelesene Nachrichten</div>
                            @endif
                            <div>Gültig bis: {{ $invalid_at->format('d.m.Y H:i') }} <a href="{{ route('cardmarket.callback.update') }}" class="btn btn-sm btn-link" title="Erneuern"><i class="fas fa-fw fa-sync"></i></a></div>
                            <form action="{{ route('cardmarket.callback.destroy') }}" class="ml-1 mt-3" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-secondary" title="Aktualisieren">Konto trennen</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @else
            @if ($paidOrders_count > 0)
                <div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0">
                    <div class="card h-100">
                        <div class="card-header">Bezahlte Bestellungen</div>
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Bestellung</th>
                                        <th width="100"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paidOrders as $order)
                                        <tr>
                                            <td class="align-middle">{{ $order->paid_at }}</td>
                                            <td class="align-middle">
                                                <a href="{{ $order->path }}">{{ $order->cardmarket_order_id }}</a>
                                                <div class="text-muted">{{ $order->buyer->name }}</div>
                                            </td>
                                            <td class="align-middle">
                                                <div>{{ $order->revenue_formatted }} € </div>
                                                <div>{{ $order->articles_count }} Artikel</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <home-article-index :is-syncing-articles="{{ $is_syncing_articles }}"></home-article-index>

            <div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0">
                <div class="card h-100">
                    <div class="card-header">Letzte Bewertungen</div>
                    <div class="card-body" style="max-height: 250px; overflow: auto;">
                        @if (count($evaluations))
                            <table class="table table-striped table-hover">
                                <tbody>
                                    @foreach ($evaluations as $evaluation)
                                        <tr>
                                            <td class="align-middle">{{ $evaluation->order->received_at->format('d.m.Y H:i') }}</td>
                                            <td class="align-middle">
                                                <a href="{{ $evaluation->order->path }}">{{ $evaluation->order->cardmarket_order_id }}</a>
                                                <div class="text-muted">{{ $evaluation->order->buyer->name }}</div>
                                            </td>
                                            <td class="align-middle text-center" width="35">{{ $evaluation->grade }}</td>
                                            <td class="align-middle text-center" width="35">{{ $evaluation->item_description }}</td>
                                            <td class="align-middle text-center" width="35">{{ $evaluation->packaging }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-dark" role="alert">
                                Noch keine Bewertungen vorhanden.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
    <home-order-index></home-order-index>
@endsection
