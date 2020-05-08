@extends('layouts.app')

@section('content')

    <div class="d-flex mb-1">
        <h2 class="col mb-0"><a class="text-body" href="/item">{{ __('app.nav.settings') }}</a><span class="d-none d-md-inline"> > {{ $model->name }}</span></h2>
        <div class="d-flex align-items-center">
            <a href="/home" class="btn btn-secondary ml-1">{{ __('app.overview') }}</a>
        </div>
    </div>
        <div class="row">
            <div class="col-md-6">
                <form action="{{ $model->path }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card mb-5">
                        <div class="card-header">{{ __('user.edit.personalization') }}</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="prepared_message">{{ __('user.edit.locale') }}</label>
                                <select class="form-control" id="locale" name="locale">
                                    @foreach($locales as $locale)
                                        <option value="{{ $locale['lang'] }}" {{ ($model->locale == $locale['lang'] ? 'selected="selected"' : '') }}>{{ $locale['name'] }} - {{ $locale['name_orig'] }}</option>
                                    @endforeach
                                </select>
                                @error('locale')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ __('app.actions.save') }}</button>
                        </div>
                    </div>
                </form>

                <form action="{{ $model->path }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card mb-5">
                        <div class="card-header">{{ __('auth.password') }}</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="password">{{ __('auth.password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">{{ __('auth.password_reset_password_confirm') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ __('app.actions.save') }}</button>
                        </div>
                    </div>
                </form>

                <form action="/user/settings/api_token" method="POST">
                    @csrf

                    <div class="card mb-5">
                        <div class="card-header">API Token</div>
                        <div class="card-body">
                            {{ $model->api_token ?: 'Kein Token vorhanden' }}
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Token generieren</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form action="{{ $model->path }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card mb-5">
                        <div class="card-header">{{ __('user.edit.prepared_message') }}</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="prepared_message">{{ __('user.edit.personalization') }}</label>
                                <textarea class="form-control @error('password') is-invalid @enderror" id="prepared_message" name="prepared_message" rows="12">{!! $model->prepared_message !!}</textarea>
                                @error('prepared_message')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ __('app.actions.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

@endsection