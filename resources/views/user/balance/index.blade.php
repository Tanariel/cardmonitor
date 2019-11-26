@extends('layouts.app')

@section('content')

    <h2>Guthaben</h2>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card mb-5">
                <div class="card-header">Guthaben</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-4"><b>Guthaben</b></div>
                                <div class="col-md-8">{{ $model->balance_in_euro_formatted }} €</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">Konto aufladen</div>
                <div class="card-body">
                    <p>Überweise einfach Guthaben in gewünschter Höhe auf dein Cardmonitor-Konto:</p>
                    <div class="row">
                        <div class="col-md-4"><b>Kontoinhaber</b></div>
                        <div class="col-md-8">Daniel Sundermeier</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>IBAN</b></div>
                        <div class="col-md-8">DE25 1203 0000 1059 2689 77</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>Institut</b></div>
                        <div class="col-md-8">DKB Deutsche Kreditbank</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>BIC</b></div>
                        <div class="col-md-8">BYLADEM1001</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>Verwendungszweck</b></div>
                        <div class="col-md-8">Cardmonitor {{ $model->id }}</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <user-balance-table></user-balance-table>

@endsection