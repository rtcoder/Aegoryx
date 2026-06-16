<?php

return [
    'default_override_reason' => 'Configuración manual de acceso del tenant.',
    'save_access' => 'Guardar acceso',
    'tenant_access' => 'Acceso a funciones',
    'tenant_access_description' => 'Las funciones disponibles están fijadas en el código de la aplicación. La base de datos solo guarda lo que este tenant tiene habilitado.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Páginas, revisiones y snapshots de contenido publicado.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Contactos, empresas, deals e historial de actividad.',
        ],
        'files' => [
            'label' => 'Archivos',
            'description' => 'Archivos privados, metadatos y control de acceso.',
        ],
    ],
];
