@extends('layouts.app')

@section('content')

    <div class="d-flex">
        <h2 class="col mb-0"><a class="text-body" href="/item">{{ __('app.nav.item') }}</a><span class="d-none d-md-inline"> > {{ $model->name }}</span></h2>
        <div class="d-flex align-items-center">
            <a href="{{ $model->path }}" class="btn btn-secondary ml-1">{{ __('app.overview') }}</a>
        </div>
    </div>
    <form action="{{ $model->path }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-5">
                    <div class="card-header">{{ $model->name }}</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('app.name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('app.name') }}" value="{{ $model->name }}" {{ $model->isEditable() ? '' : 'readonly="readonly"' }}>
                        </div>
                        <div class="form-group">
                            <label for="unit_cost_formatted">{{ __('item.unit_cost') }}</label>
                            <input type="text" class="form-control" id="unit_cost_formatted" name="unit_cost_formatted" value="{{ $model->unit_cost_formatted }}">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('app.actions.save') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

@endsection