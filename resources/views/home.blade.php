@extends('layouts.app')

@section('content')
    <div class="row mb-3 align-items-stretch">

        <div class="col">
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

        <div class="col">
            <div class="card h-100">
                <div class="card-header">Kennzahl</div>
                <div class="card-body">

                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-header">Kennzahl</div>
                <div class="card-body">

                </div>
            </div>
        </div>

        <div class="col">
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
