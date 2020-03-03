@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('cardmarket.create.header') }}</div>

                <div class="card-body">
                    <a href="{{ $link }}">{{ __('cardmarket.create.header') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection