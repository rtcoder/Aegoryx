<?php

namespace App\Modules\Audit\Enums;

enum AuditLogAction: string
{
    case BillingSubscriptionSynced = 'billing_subscription_synced';
    case LicenseVerified = 'license_verified';
    case SupportSessionEnded = 'support_session_ended';
    case SupportSessionExpired = 'support_session_expired';
    case SupportSessionStarted = 'support_session_started';
    case TenantFeatureOverrideSet = 'tenant_feature_override_set';
    case TenantStatusChanged = 'tenant_status_changed';
    case TwoFactorDisabled = 'two_factor_disabled';
    case TwoFactorEnabled = 'two_factor_enabled';
}
