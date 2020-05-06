@extends('layouts.app')

@section('content')

    <h2>Karten > Export</h2>
    <card-export-index
        :expansions="{{ json_encode($expansions) }}"
        :languages="{{ json_encode($languages) }}"
        >
    </card-export-index>

@endsection