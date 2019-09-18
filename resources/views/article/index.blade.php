@extends('layouts.app')

@section('content')

    <h2>Artikel</h2>
    <article-table :conditions="{{ json_encode($conditions) }}" :languages="{{ json_encode($languages) }}"></article-table>

@endsection