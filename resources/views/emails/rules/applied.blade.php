@component('mail::message')
# Regel angewendet

{{ $user->name }} hat Regeln in {{ $runtime_in_sec }}s angewendet.<br />
Es waren {{ $synced_count }} Artikel betroffen.

{{ config('app.name') }}
@endcomponent
