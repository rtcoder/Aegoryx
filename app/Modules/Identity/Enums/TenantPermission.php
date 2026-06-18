<?php

namespace App\Modules\Identity\Enums;

enum TenantPermission: string
{
    case ManageUsers = 'manage_users';
    case ManageContent = 'manage_content';
    case PublishContent = 'publish_content';
    case ManageCrm = 'manage_crm';
    case ManageFiles = 'manage_files';
    case ExportActivity = 'export_activity';
    case ManageSettings = 'manage_settings';
}
