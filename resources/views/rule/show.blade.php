@extends('layouts.app')

@section('content')

    <div class="d-flex mb-1">
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
        <div class="col-12 col-md-6">
            <div class="card mb-5">
                <div class="card-header">{{ $model->name }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-label"><b>Name</b></div>
                                <div class="col-value">{{ $model->name }}</div>
                            </div>
                            <div>
                                {!! nl2br($model->description) !!}
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-label"><b>Erweiterung</b></div>
                                <div class="col-value">{{ $model->expansion_id ? $model->expansion->name : 'Alle' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-label"><b>Seltenheit</b></div>
                                <div class="col-value">{{ $model->rarity ?? 'Alle' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-label"><b>Preis</b></div>
                                <div class="col-value">{{ $model->base_price_formatted }} * {{ $model->multiplier_formatted }}</div>
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

        <div class="col-12 col-md-6">
            <div class="card mb-5">
                <div class="card-header">Artikel</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-label"><b>Artikel</b></div>
                        <div class="col-value">{{ $model->articleStats->count_formatted }}</div>
                        <div class="col-info"></div>
                    </div>
                    <div class="row">
                        <div class="col-label"><b>Verkaufspreis</b></div>
                        <div class="col-value">{{ $model->articleStats->price_formatted }} €</div>
                        <div class="col-info"></div>
                    </div>
                    <div class="row">
                        <div class="col-label"><b>Regelpreis</b></div>
                        <div class="col-value">{{ $model->articleStats->price_rule_formatted }} €</div>
                        <div class="col-info">
                            @if ($model->articleStats->difference != 0)
                                {!! $model->articleStats->difference_icon !!} {{ $model->articleStats->difference_percent_formatted }}%
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-dark mt-3" role="alert">
                        Regeln müssen erst simuliert oder angewendet werden, um hier eine Änderung zu sehen.
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection