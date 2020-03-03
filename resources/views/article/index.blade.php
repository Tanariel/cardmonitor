@extends('layouts.app')

@section('content')

    <h2>{{ __('app.nav.article') }}</h2>
    <article-table
        :conditions="{{ json_encode($conditions) }}"
        :expansions="{{ json_encode($expansions) }}"
        :games="{{ json_encode($games) }}"
        :is-applying-rules="{{ $is_applying_rules }}"
        :is-syncing-articles="{{ $is_syncing_articles }}"
        :languages="{{ json_encode($languages) }}"
        :rarities="{{ json_encode($rarities) }}"
        :rules="{{ json_encode($rules) }}"
        :storages="{{ json_encode($storages) }}"
    ></article-table>

@endsection