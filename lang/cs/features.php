<?php

return [
    'default_override_reason' => 'Ruční konfigurace přístupu tenanta.',
    'save_access' => 'Uložit přístup',
    'tenant_access' => 'Přístup k funkcím',
    'tenant_access_description' => 'Dostupné funkce jsou pevně definované v kódu aplikace. Databáze ukládá pouze to, co má tento tenant zapnuté.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Stránky, revize a snapshoty publikovaného obsahu.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Kontakty, firmy, dealy a historie aktivity.',
        ],
        'files' => [
            'label' => 'Soubory',
            'description' => 'Soukromé soubory, metadata a řízení přístupu.',
        ],
    ],
];
