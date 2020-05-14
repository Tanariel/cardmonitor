@extends('layouts.app')

@section('content')

    <h2>Karten > Export</h2>
    <card-export-index
        :expansions="{{ json_encode($expansions) }}"
        :skryfall-expansions="{{ json_encode($skryfall_expansions) }}"
        :languages="{{ json_encode($languages) }}"
        >
    </card-export-index>

@endsection