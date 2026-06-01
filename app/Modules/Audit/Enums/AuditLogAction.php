<?php

namespace App\Modules\Audit\Enums;

enum AuditLogAction: string
{
    case FeatureCreated = 'feature_created';
    case FeatureStatusChanged = 'feature_status_changed';
    case LicenseVerified = 'license_verified';
    case SupportSessionEnded = 'support_session_ended';
    case SupportSessionExpired = 'support_session_expired';
    case SupportSessionStarted = 'support_session_started';
    case TenantFeatureOverrideSet = 'tenant_feature_override_set';
    case TenantStatusChanged = 'tenant_status_changed';
}
