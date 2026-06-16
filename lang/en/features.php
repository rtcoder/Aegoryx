<?php

return [
    'default_override_reason' => 'Manual tenant access configuration.',
    'save_access' => 'Save access',
    'tenant_access' => 'Feature access',
    'tenant_access_description' => 'Available features are fixed in application code. The database stores only what this tenant has enabled.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Pages, revisions, and published content snapshots.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Contacts, companies, deals, and activity history.',
        ],
        'files' => [
            'label' => 'Files',
            'description' => 'Private files, metadata, and access control.',
        ],
    ],
];
