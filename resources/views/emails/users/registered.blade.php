@component('mail::message')
# Neuer Nutzer

{{ $user->name }} hat sich mit der E-Mail {{ $user->email }} angemeldet.

{{ config('app.name') }}
@endcomponent
