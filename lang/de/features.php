<?php

return [
    'default_override_reason' => 'Manuelle Zugriffskonfiguration für den Mandanten.',
    'save_access' => 'Zugriff speichern',
    'tenant_access' => 'Feature-Zugriff',
    'tenant_access_description' => 'Verfügbare Features sind fest im Anwendungscode definiert. Die Datenbank speichert nur, was dieser Mandant aktiviert hat.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Seiten, Revisionen und veröffentlichte Inhalts-Snapshots.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Kontakte, Firmen, Deals und Aktivitätshistorie.',
        ],
        'files' => [
            'label' => 'Dateien',
            'description' => 'Private Dateien, Metadaten und Zugriffskontrolle.',
        ],
    ],
];
