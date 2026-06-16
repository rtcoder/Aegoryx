<?php

namespace App\Modules\Entitlements\Enums;

enum SystemFeature: string
{
    case Cms = 'cms';
    case Crm = 'crm';
    case Files = 'files';

    public function label(): string
    {
        return __("features.registry.{$this->value}.label");
    }

    public function description(): string
    {
        return __("features.registry.{$this->value}.description");
    }
}
