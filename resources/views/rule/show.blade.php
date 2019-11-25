@extends('layouts.app')

@section('content')

    <div class="d-flex">
        <h2 class="col"><a class="text-body" href="/item">Regel</a> > {{ $model->name }}</h2>
        <div class="d-flex align-items-center">
            <a href="{{ $model->editPath }}" class="btn btn-primary" title="Bearbeiten"><i class="fas fa-edit"></i></a>
            <a href="/rule" class="btn btn-secondary ml-1">Übersicht</a>
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
                            <div>
                                {!! nl2br($model->description) !!}
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-4"><b>Erweiterung</b></div>
                                <div class="col-md-8">{{ $model->expansion_id ? $model->expansion->name : 'Alle' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><b>Seltenheit</b></div>
                                <div class="col-md-8">{{ $model->rarity ?? 'Alle' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><b>Preis</b></div>
                                <div class="col-md-8">{{ $model->base_price_formatted }} * {{ $model->multiplier_formatted }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        @if ($model->isActivated())
                            <form action="{{ $model->path }}/activate" class="ml-1" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-secondary" title="Deaktivieren">Deaktivieren</button>
                            </form>
                        @else
                            <form action="{{ $model->path }}/activate" class="ml-1" method="POST">
                                @csrf

                                <button type="submit" class="btn btn-success" title="Aktivieren">Aktivieren</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Artikel</div>
        <div class="card-body">

        </div>
    </div>

@endsection