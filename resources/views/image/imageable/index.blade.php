@extends('layouts.guest')

@section('content')
    <div class="container">

        <div class="row">

            <div class="col">
                <h2>{{ (count($model->images) == 1 ? 'Bild' : 'Bilder') }} zu deiner Bestellung {{ $model->cardmarket_order_id }}</h2>
            </div>

        </div>

        <imageable-gallery :model="{{ json_encode($model) }}"></imageable-gallery>

    </div>
@endsection