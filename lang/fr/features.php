<?php

return [
    'default_override_reason' => 'Configuration manuelle de l’accès du tenant.',
    'save_access' => 'Enregistrer l’accès',
    'tenant_access' => 'Accès aux fonctionnalités',
    'tenant_access_description' => 'Les fonctionnalités disponibles sont fixées dans le code de l’application. La base de données stocke uniquement ce que ce tenant a activé.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Pages, révisions et snapshots de contenu publié.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Contacts, entreprises, affaires et historique d’activité.',
        ],
        'files' => [
            'label' => 'Fichiers',
            'description' => 'Fichiers privés, métadonnées et contrôle d’accès.',
        ],
    ],
];
