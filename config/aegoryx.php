<?php

use App\Modules\AdminConsole\Providers\AdminConsoleServiceProvider;
use App\Modules\Audit\Providers\AuditServiceProvider;
use App\Modules\Auth\Providers\AuthServiceProvider;
use App\Modules\Billing\Providers\BillingServiceProvider;
use App\Modules\Cms\Providers\CmsServiceProvider;
use App\Modules\Crm\Providers\CrmServiceProvider;
use App\Modules\Entitlements\Providers\EntitlementsServiceProvider;
use App\Modules\Files\Providers\FilesServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Licensing\Providers\LicensingServiceProvider;
use App\Modules\PublicApi\Providers\PublicApiServiceProvider;
use App\Modules\Security\Providers\SecurityServiceProvider;
use App\Modules\Tenancy\Providers\TenancyServiceProvider;
use App\Modules\TenantPanel\Providers\TenantPanelServiceProvider;

return [
    'landlord' => [
        'domain' => env('LANDLORD_DOMAIN', 'admin.aegoryx.test'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Aegoryx Modules
    |--------------------------------------------------------------------------
    |
    | Modules are registered explicitly to keep the modular monolith readable.
    | Each module provider may load routes, policies, commands, and bindings for
    | its own bounded context without coupling unrelated domains together.
    |
    */
    'modules' => [
        'tenancy' => [
            'name' => 'Tenancy',
            'enabled' => true,
            'provider' => TenancyServiceProvider::class,
        ],
        'identity' => [
            'name' => 'Identity',
            'enabled' => true,
            'provider' => IdentityServiceProvider::class,
        ],
        'auth' => [
            'name' => 'Auth',
            'enabled' => true,
            'provider' => AuthServiceProvider::class,
        ],
        'security' => [
            'name' => 'Security',
            'enabled' => true,
            'provider' => SecurityServiceProvider::class,
        ],
        'entitlements' => [
            'name' => 'Entitlements',
            'enabled' => true,
            'provider' => EntitlementsServiceProvider::class,
        ],
        'licensing' => [
            'name' => 'Licensing',
            'enabled' => true,
            'provider' => LicensingServiceProvider::class,
        ],
        'billing' => [
            'name' => 'Billing',
            'enabled' => true,
            'provider' => BillingServiceProvider::class,
        ],
        'cms' => [
            'name' => 'Cms',
            'enabled' => true,
            'provider' => CmsServiceProvider::class,
        ],
        'crm' => [
            'name' => 'Crm',
            'enabled' => true,
            'provider' => CrmServiceProvider::class,
        ],
        'files' => [
            'name' => 'Files',
            'enabled' => true,
            'provider' => FilesServiceProvider::class,
        ],
        'audit' => [
            'name' => 'Audit',
            'enabled' => true,
            'provider' => AuditServiceProvider::class,
        ],
        'public-api' => [
            'name' => 'PublicApi',
            'enabled' => true,
            'provider' => PublicApiServiceProvider::class,
        ],
        'admin-console' => [
            'name' => 'AdminConsole',
            'enabled' => true,
            'provider' => AdminConsoleServiceProvider::class,
        ],
        'tenant-panel' => [
            'name' => 'TenantPanel',
            'enabled' => true,
            'provider' => TenantPanelServiceProvider::class,
        ],
    ],
];
