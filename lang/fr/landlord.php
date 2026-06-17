<?php

return [
    'console' => 'Console landlord',
    'admin_console' => 'Console d’administration',
    'system_controls' => 'Contrôles globaux du système Aegoryx.',
    'sign_out' => 'Se déconnecter',
    'navigation_label' => 'Navigation d’administration',
    'dashboard_title' => 'Tableau de bord landlord',
    'dashboard_description' => 'Utilisez la navigation pour gérer les tenants, l’accès aux fonctionnalités, les licences, la facturation et le mode support.',
    'support_mode_banner' => 'Mode support actif pour :tenant jusqu’à :expires.',
    'login_heading' => 'Connexion landlord',
    'login_description' => 'Utilisez votre compte superadmin.',
    'sign_in' => 'Se connecter',
    'sections' => [
        'tenants' => 'Gérer les comptes tenants, domaines, états de déploiement et points d’entrée support.',
        'licenses' => 'Examiner l’état des licences, la vérification et l’accès self-hosted.',
        'license_show' => 'État effectif de la licence et contrôles de vérification.',
        'billing' => 'Inspecter les plans, abonnements, état de facturation et synchronisation fournisseur.',
        'support' => 'Démarrer des sessions support auditées et examiner l’historique d’accès.',
        'tenant_show' => 'Détails du tenant et contrôles opérationnels.',
    ],
    'billing' => [
        'event_type' => 'Type d’événement',
        'license_statuses' => 'Statuts des licences',
        'no_events' => 'Aucun événement de facturation pour le moment.',
        'no_licenses' => 'Aucune licence à résumer pour le moment.',
        'no_subscriptions' => 'Aucun abonnement à résumer pour le moment.',
        'provider' => 'Fournisseur',
        'recent_events' => 'Événements de facturation récents',
        'recent_events_description' => 'Derniers webhooks et synchronisations de facturation sans secrets dans les payloads.',
        'subscription_statuses' => 'Statuts des abonnements',
    ],
];
