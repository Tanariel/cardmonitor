@extends('layouts.guest')

@section('content')
    <div class="container d-flex flex-column" style="height: calc(100vh - 110px);">

        <div class="row">

            <div class="col">
                <h2>{{ __('image.imageable.index.heading', ['order' => $model->cardmarket_order_id]) }}</h2>
            </div>

        </div>

        <imageable-gallery class="flex-grow-1 d-flex flex-column" :model="{{ json_encode($model) }}"></imageable-gallery>

    </div>
@endsection