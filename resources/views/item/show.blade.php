@extends('layouts.app')

@section('content')

    <div class="d-flex">
        <h2 class="col"><a class="text-body" href="/item">Kosten</a> > {{ $model->name }}</h2>
        <div class="d-flex align-items-center">
            <a href="{{ url($model->path . '/edit') }}" class="btn btn-primary" title="Bearbeiten"><i class="fas fa-edit"></i></a>
            <a href="{{ url('/item') }}" class="btn btn-secondary ml-1">Übersicht</a>
            @if ($model->isDeletable())
                <form action="{{ $model->path }}" class="ml-1" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger" title="Löschen"><i class="fas fa-trash"></i></button>
                </form>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-5">
                <div class="card-header">{{ $model->name }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-4"><b>Name</b></div>
                                <div class="col-md-8">{{ $model->name }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><b>Kosten / Einheit</b></div>
                                <div class="col-md-8">{{ number_format($model->unit_cost, 2, ',', '.') }} €</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Staffelung</div>
        <div class="card-body">
            <item-quantity-table :model="{{ json_encode($model) }}"></item-quantity-table>
        </div>
    </div>


@endsection