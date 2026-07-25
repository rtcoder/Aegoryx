<?php

return [
    'default_override_reason' => 'Ручная настройка доступа tenant.',
    'clear_override' => 'Очистить ручное переопределение',
    'effective_reason' => 'Причина',
    'effective_source' => 'Источник',
    'save_access' => 'Сохранить доступ',
    'source_labels' => [
        'license' => 'лицензия',
        'manual' => 'вручную',
        'none' => 'нет',
        'plan' => 'план',
        'system' => 'система',
        'trial' => 'trial',
    ],
    'tenant_access' => 'Доступ к функциям',
    'tenant_access_description' => 'Доступные функции зафиксированы в коде приложения. База данных хранит только то, что включено для этого tenant.',
    'registry' => [
        'cms' => [
            'label' => 'CMS',
            'description' => 'Страницы, ревизии и опубликованные snapshots контента.',
        ],
        'crm' => [
            'label' => 'CRM',
            'description' => 'Контакты, компании, сделки и история активности.',
        ],
        'files' => [
            'label' => 'Файлы',
            'description' => 'Приватные файлы, метаданные и контроль доступа.',
        ],
    ],
];
