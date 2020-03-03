<?php

return [
    'plural' => 'Regeln',
    'show' => [
        'alert_info' => 'Regeln müssen erst simuliert oder angewendet werden, um hier eine Änderung zu sehen.',
    ],
    'apply' => 'Regeln anwenden',
    'simulate' => 'Regeln simulieren',
    'modal_apply' => [
        'body' => [
            'question' => '',
            'comment' => 'Der Prozess läuft maximal 1 Stunde. Das entspricht etwa 30.000 Artikeln',
            'alert' => [
                'text' => 'Es werden Preise in deinem Cardmarket Konto verändert! Versichere dich vorher, ob alle Regeln angewendet werden, wie Du es möchtest!',
                'danger' => 'Ausführung auf eigene Gefahr!',
            ],
        ],
    ],
    'description' => 'Beschreibung',
    'price_base' => 'Basispreis',
    'multiplikator' => 'Multiplikator',
    'alerts' => [
        'no_data' => 'Keine Regeln vorhanden',
    ],
    'successes' => [
        'activated' => 'Regel {rule} wurde aktiviert.',
        'deactivated' => 'Regel {rule} wurde deaktiviert.',
        'simulate_background' => 'Regeln werden im Hintergrund simuliert.',
        'simulated' => 'Regeln wurden simuliert.',
    ],
    'errors' => [
        'simulated' => 'Regeln konnten nicht simuliert werden!',
    ],
];