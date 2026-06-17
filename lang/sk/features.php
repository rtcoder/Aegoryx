<?php

return [
    'default_override_reason' => 'Ručná konfigurácia prístupu tenanta.',
    'save_access' => 'Uložiť prístup',
    'tenant_access' => 'Prístup k funkciám',
    'tenant_access_description' => 'Dostupné funkcie sú pevne definované v kóde aplikácie. Databáza ukladá iba to, čo má tento tenant zapnuté.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Stránky, revízie a snapshoty publikovaného obsahu.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Kontakty, firmy, dealy a história aktivity.',
        ],
        'files' => [
            'label' => 'Súbory',
            'description' => 'Súkromné súbory, metadata a riadenie prístupu.',
        ],
    ],
];
