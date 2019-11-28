@component('mail::message')
# Geld eingzahlt

Es wurden {{ $balance->amount_in_euro_formatted }} € mit dem Buchungstext "{{ $balance->description }}"
@if ($balance->user)
    von {{ $balance->user->name }}
@endif
eingezahlt

{{ config('app.name') }}
@endcomponent
