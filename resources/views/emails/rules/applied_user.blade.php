@component('mail::message')
# Deine Regel wurden angewendet

@component('mail::table')
| Regel                 | Anzahl Karten                  |
| --------------------- | ------------------------------:|
@foreach ($results as $result)
| {{ $result['name'] }} | {{ $result['affected_rows'] }} |
@endforeach
@endcomponent

Die Regeln wurden in {{ $runtime_in_sec }}s angewendet.

{{ config('app.name') }}
@endcomponent
