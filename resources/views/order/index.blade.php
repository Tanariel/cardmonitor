@extends('layouts.app')

@section('content')

    <h2>{{ __('app.nav.order') }}</h2>
    <order-table :is-syncing-orders="{{ $is_syncing_orders }}" :states="{{ json_encode($states) }}"></order-table>

@endsection