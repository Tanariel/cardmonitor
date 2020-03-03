<?php

return [
    'evaluations' => [
        'singular' => 'Bewertung',
        'grade' => 'Allgemeine Bewertung',
        'item_description' => 'Beschreibung der Artikelzustände',
        'packaging' => 'Verpackung der Bestellung',
        'comment' => 'Kommentar',
        'complaint' => 'Beschwerden',
        '1' => 'Sehr gut',
        '2' => 'Gut',
        '3' => 'Neutral',
        '4' => 'Schlecht'
    ],
    'states' => [
        'bought' => 'Unbezahlt',
        'paid' => 'Bezahlt',
        'sent' => 'Versandt',
        'received' => 'Angekommen',
        'evaluated' => 'Bewertet',
        'lost' => 'Nicht Angekommen',
        'cancelled' => 'Storniert',
    ],
    'show' => [
        'message_modal' => [
            'title' => 'Nachricht an :buyer versenden',
        ],
    ],
    'successes' => [
        'synced' => 'Bestellungen wurden synchronisiert.',
        'send' => 'Bestellung wurde verschickt.',
        'syncing_background' => 'Bestellungen werden im Hintergrund aktualisiert.',
    ],
    'errors' => [
        'synced' => 'Bestellungen konnten nicht synchronisiert werden! Ist das Cardmarket Konto verbunden?',
        'send' => 'Bestellung konnten nicht verschickt werden!',
        'loaded' => 'Bestellungen konnten nicht geladen werden!',
    ],
    'alerts' => [
        'no_data' => 'Keine Bestellungen vorhanden',
    ],
    'id' => 'Bestellnummer',
    'buyer' => 'Käufer',
    'seller' => 'Verkäufer',
    'shipping_address' => 'Versandadresse',
    'calculation' => 'Kalkulation',
    'plural' => 'Bestellungen',
    'singular' => 'Bestellung',
    'home' => [
        'per' => [
            'day' => 'Pro Tag',
            'month' => 'Pro Monat',
            'order' => 'Pro Bestellung',
            'card' => 'Pro Karte',
        ],
        'month' => [
            'title' => 'Bestellungen pro Tag',
            'chart' => [
                'title' => 'Bestellungen im :month',
            ],
            'errors' => [
                'no_data' => 'Keine Bestellungen im {month} vorhanden',
            ],
        ],
         'year' => [
            'title' => 'Bestellungen pro Jahr',
            'latest' => 'Letzte 12 Monate',
            'chart' => [
                'title' => 'Bestellungen in :year',
                'title_latest' => 'Bestellungen der letzten 12 Monate',
            ],
            'errors' => [
                'no_data' => 'Keine Bestellungen im Zeitraum vorhanden',
            ],
        ],
        'paid' => [
            'title' => 'Bezahlte Bestellungen',
        ],
    ],
    'article' => [
        'show' => [
            'actions' => [
                'next' => 'Weiter',
                'next_ok' => 'Nächste Karte (Status OK)',
                'next_problem' => 'Nächste Karte (Status Problem)',
            ],
            'alerts' => [
                'no_open_cards' => 'Alle Karten bearbeitet.',
            ],
            'problems' => [
                'plural' => 'Probleme',
                'singular' => 'Problem',
                'label' => 'Probleme?',
                'placeholder' => 'Problem auswählen',
                'not_available' => 'ist nicht vorhanden',
                'wrong_condition' => 'ist in schlechterem Zusatnd als angegeben',
                'wrong_language' => 'ist in falscher Sprache',
            ],
            'state_comments' => [
                'label' => 'Status Kommentar',
                'placeholder' => 'Kommentar für Nachricht',
            ],
        ],
        'table' => [
            'open' => 'Offen',
            'ok' => 'OK',
        ],
    ],
];