@extends('layouts.app')

@section('content')

    <h2>{{ __('app.nav.article') }} Bestand</h2>
    <div class="alert alert-primary" role="alert">
        Vor dem Ändern der Bestände zuerst alle Artikel synchronisieren!
    </div>
    <article-stock-table
        :conditions="{{ json_encode($conditions) }}"
        :expansions="{{ json_encode($expansions) }}"
        :games="{{ json_encode($games) }}"
        :is-applying-rules="{{ $is_applying_rules }}"
        :is-syncing-articles="{{ $is_syncing_articles }}"
        :languages="{{ json_encode($languages) }}"
        :rarities="{{ json_encode($rarities) }}"
    ></article-stock-table>

    @include('article.stock.import.create')

@endsection