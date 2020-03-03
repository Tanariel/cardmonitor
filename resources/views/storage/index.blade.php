@extends('layouts.app')

@section('content')

    <h2>{{ __('app.nav.storages') }}</h2>
    <div class="alert alert-dark d-none d-sm-block mt-3" role="alert">
        {{ __('storages.index.alert_info') }}
    </div>
    <storage-table></storage-table>

@endsection