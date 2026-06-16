<?php

return [
    'default_override_reason' => 'Ręczna konfiguracja dostępu tenanta.',
    'save_access' => 'Zapisz dostęp',
    'tenant_access' => 'Dostęp do funkcji',
    'tenant_access_description' => 'Dostępne funkcje są stałe w kodzie aplikacji. W bazie zapisujemy tylko to, co ten tenant ma włączone.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Strony, rewizje i publikowane snapshoty treści.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Kontakty, firmy, deale i historia aktywności.',
        ],
        'files' => [
            'label' => 'Pliki',
            'description' => 'Prywatne pliki, metadane i kontrola dostępu.',
        ],
    ],
];
