@extends('layouts.app')

@section('content')

    <h2>Artikel</h2>
    <article-table
        :conditions="{{ json_encode($conditions) }}"
        :expansions="{{ json_encode($expansions) }}"
        :languages="{{ json_encode($languages) }}"
        :rarities="{{ json_encode($rarities) }}"
    ></article-table>

@endsection