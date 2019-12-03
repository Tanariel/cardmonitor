@component('mail::message')
Hallo {{ $balance->user->name }},

Wir haben soeben das Guthaben für dein Cardmonitor Konto mit {{ $balance->amount_in_euro_formatted }} € aufgeladen.<br />
Dein Guthaben beträgt damit nun {{ $balance->user->balance_in_euro_formatted }} €.

{{ config('app.name') }}
@endcomponent
