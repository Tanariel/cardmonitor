@extends('layouts.app')

@section('content')
    <div class="row mb-3">

        <div class="col">
            <div class="card">
                <div class="card-header">Kennzahl</div>
                <div class="card-body">

                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">Kennzahl</div>
                <div class="card-body">

                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">Kennzahl</div>
                <div class="card-body">

                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
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
