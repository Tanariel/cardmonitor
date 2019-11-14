@extends('layouts.app')

@section('content')

    <div class="d-flex">
        <h2 class="col"><a class="text-body" href="/item">Lagerplatz</a> > {{ $model->name }}</h2>
        <div class="d-flex align-items-center">
            <a href="{{ $model->editPath }}" class="btn btn-primary" title="Bearbeiten"><i class="fas fa-edit"></i></a>
            <a href="/storage" class="btn btn-secondary ml-1">Übersicht</a>
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
                        </div>
                    </div>
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

    <div class="card mt-3">
        <div class="card-header">Inhalt</div>
        <div class="card-body">

        </div>
    </div>


@endsection