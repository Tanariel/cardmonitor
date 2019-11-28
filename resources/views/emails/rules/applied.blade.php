@component('mail::message')
# Regel angewendet

{{ $user->name }} hat Regeln in {{ $runtime_in_sec }}s angewendet.

{{ config('app.name') }}
@endcomponent
