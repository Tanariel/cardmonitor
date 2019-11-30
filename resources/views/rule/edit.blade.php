@extends('layouts.app')

@section('content')

    <div class="d-flex mb-1">
        <h2 class="col mb-0"><a class="text-body" href="/item">Regel</a><span class="d-none d-md-inline"> > {{ $model->name }}</span></h2>
        <div class="d-flex align-items-center">
            <a href="{{ $model->path }}" class="btn btn-secondary ml-1">Übersicht</a>
        </div>
    </div>

    <rule-edit
        :base-prices="{{ json_encode($basePrices) }}"
        :expansions="{{ json_encode($expansions) }}"
        :model="{{ json_encode($model) }}"
        :rarities="{{ json_encode($rarities) }}">
    </rule-edit>

@endsection