@extends('layouts.app')

@section('content')

    <h2>{{ __('app.nav.rule') }}</h2>
    <rule-table :is-applying-rules="{{ $is_applying_rules }}"></rule-table>

@endsection