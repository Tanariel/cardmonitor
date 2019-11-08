@extends('layouts.app')

@section('content')
    <div class="row mb-3 align-items-stretch">

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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paidOrders as $order)
                                    <tr>
                                        <td>{{ $order->paid_at }}</td>
                                        <td><a href="{{ $order->path }}">{{ $order->cardmarket_order_id }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0">
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

        <div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0 d-none d-xl-block">
            <div class="card h-100">
                <div class="card-header">Bestellungen</div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <td>Unbezahlt</td>
                                <td class="text-right">{{ $ordersByState['bought'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Bezahlt</td>
                                <td class="text-right">{{ $ordersByState['paid'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Versendet</td>
                                <td class="text-right">{{ $ordersByState['sent'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Angekommen</td>
                                <td class="text-right">{{ $ordersByState['received'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Bewertet</td>
                                <td class="text-right">{{ $ordersByState['evaluated'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Verloren</td>
                                <td class="text-right">{{ $ordersByState['lost'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Storniert</td>
                                <td class="text-right">{{ $ordersByState['canceled'] ?? 0 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0 d-none d-xl-block">
            <div class="card h-100">
                <div class="card-header">Artikel</div>
                <div class="card-body">
                    Anzahl Artikel, Kosten, Wert, Chart mit Preisentwicklung?
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-header">Letzte Bewertungen</div>
                <div class="card-body">
                    @if (count($evaluations))
                        <table class="table table-striped table-hover">
                            <thead>
                                <th>Datum</th>
                                <th>Käufer</th>
                                <th>Bestellung</th>
                                <th colspan="3">Bewertung</th>
                            </thead>
                            <tbody>
                                @foreach ($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->order->received_at->format('d.m.Y H:i') }}</td>
                                        <td>{{ $evaluation->order->buyer->name }}</td>
                                        <td><a href="{{ $evaluation->order->path }}">{{ $evaluation->order->cardmarket_order_id }}</a></td>
                                        <td>{{ $evaluation->grade }}</td>
                                        <td>{{ $evaluation->item_description }}</td>
                                        <td>{{ $evaluation->packaging }}</td>
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

    </div>
    <home-order-index></home-order-index>
@endsection
