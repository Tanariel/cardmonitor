@extends('layouts.app')

@section('content')

    <h2>Artikel hinzufügen</h2>
    <article-create
        :conditions="{{ json_encode($conditions) }}"
        :default-card-costs="{{ json_encode($defaultCardCosts) }}"
        :expansions="{{ json_encode($expansions) }}"
        :languages="{{ json_encode($languages) }}"
        :storages="{{ json_encode($storages) }}"
        >
    </article-create>

@endsection