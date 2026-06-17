<?php

return [
    'console' => 'Консоль landlord',
    'admin_console' => 'Консоль администратора',
    'system_controls' => 'Системные настройки Aegoryx.',
    'sign_out' => 'Выйти',
    'navigation_label' => 'Навигация администратора',
    'dashboard_title' => 'Панель landlord',
    'dashboard_description' => 'Используйте навигацию для управления tenants, доступом к функциям, лицензиями, биллингом и режимом поддержки.',
    'support_mode_banner' => 'Режим поддержки активен для :tenant до :expires.',
    'login_heading' => 'Вход landlord',
    'login_description' => 'Используйте учетную запись superadmin.',
    'sign_in' => 'Войти',
    'sections' => [
        'tenants' => 'Управляйте учетными записями tenants, доменами, состоянием развертывания и точками входа поддержки.',
        'licenses' => 'Просматривайте состояние лицензий, статус проверки и self-hosted доступ.',
        'license_show' => 'Эффективное состояние лицензии и элементы проверки.',
        'billing' => 'Проверяйте планы, подписки, состояние биллинга и синхронизацию с провайдером.',
        'support' => 'Запускайте аудируемые сессии поддержки и просматривайте историю доступа.',
        'tenant_show' => 'Детали tenant и операционные элементы управления.',
    ],
    'billing' => [
        'event_type' => 'Тип события',
        'license_statuses' => 'Статусы лицензий',
        'no_events' => 'Событий биллинга пока нет.',
        'no_licenses' => 'Лицензий для сводки пока нет.',
        'no_subscriptions' => 'Подписок для сводки пока нет.',
        'provider' => 'Провайдер',
        'recent_events' => 'Последние события биллинга',
        'recent_events_description' => 'Последние webhooks и синхронизации биллинга без секретов в payloads.',
        'subscription_statuses' => 'Статусы подписок',
    ],
];
