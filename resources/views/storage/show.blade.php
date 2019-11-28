@extends('layouts.app')

@section('content')

    <div class="d-flex">
        <h2 class="col"><a class="text-body" href="/item">Lagerplatz</a> > {{ $model->full_name }}</h2>
        <div class="d-flex align-items-center">
            <a href="{{ $model->editPath }}" class="btn btn-primary" title="Bearbeiten"><i class="fas fa-edit"></i></a>
            <a href="/storages" class="btn btn-secondary ml-1">Übersicht</a>
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
                <div class="card-header">{{ $model->full_name }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-4"><b>Name</b></div>
                                <div class="col-md-8">{{ $model->name }}</div>
                            </div>
                            @if ($model->parent)
                                <div class="row">
                                    <div class="col-md-4"><b>Hauptlagerplatz</b></div>
                                    <div class="col-md-8"><a class="text-body" href="{{ $model->parent->path }}">{{ $model->parent->name }}</a></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Standard Zuordnung</div>
                <div class="card-body">
                    <storage-content-table :model="{{ json_encode($model) }}"></storage-content-table>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card mb-5">
                <div class="card-header">Artikel</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4"><b>Artikel</b></div>
                        <div class="col-8">{{ $model->articleStats->count_formatted }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4"><b>Verkaufspreis</b></div>
                        <div class="col-8">{{ $model->articleStats->price_formatted }} €</div>
                    </div>
                </div>

            </div>

            @if (count($model->descendants))
                <div class="card mb-5">
                    <div class="card-header">Unterlagerplätze</div>
                    <div class="card-body">
                        @foreach ($model->descendants as $descendant)
                            <div class="">
                                <h6><a class="text-body" href="{{ $descendant->path }}">{{ $descendant->full_name }}</a></h6>
                            </div>
                            <div class="row">
                                <div class="col-4"><b>Artikel</b></div>
                                <div class="col-8">{{ $descendant->articleStats->count_formatted }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><b>Verkaufspreis</b></div>
                                <div class="col-8">{{ $descendant->articleStats->price_formatted }} €</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

@endsection