<?php

namespace App\Modules\Licensing\Support;

use App\Modules\Licensing\Enums\LicenseStatus;

final readonly class LicenseStateMatrix
{
    /**
     * @return array<string, array{allows_access: bool, keeps_data: bool, requires_subscription: bool, operator_action: string}>
     */
    public function states(): array
    {
        return [
            'trial' => [
                'allows_access' => true,
                'keeps_data' => true,
                'requires_subscription' => false,
                'operator_action' => 'convert_or_expire',
            ],
            LicenseStatus::Active->value => [
                'allows_access' => true,
                'keeps_data' => true,
                'requires_subscription' => true,
                'operator_action' => 'monitor_renewal',
            ],
            LicenseStatus::Grace->value => [
                'allows_access' => true,
                'keeps_data' => true,
                'requires_subscription' => true,
                'operator_action' => 'recover_payment_or_license',
            ],
            LicenseStatus::Expired->value => [
                'allows_access' => false,
                'keeps_data' => true,
                'requires_subscription' => true,
                'operator_action' => 'renew_or_disable_features',
            ],
            LicenseStatus::Suspended->value => [
                'allows_access' => false,
                'keeps_data' => true,
                'requires_subscription' => true,
                'operator_action' => 'manual_review_required',
            ],
            'perpetual' => [
                'allows_access' => true,
                'keeps_data' => true,
                'requires_subscription' => false,
                'operator_action' => 'verify_installation_periodically',
            ],
        ];
    }

    /**
     * @return array{allows_access: bool, keeps_data: bool, requires_subscription: bool, operator_action: string}
     */
    public function state(string|LicenseStatus $state): array
    {
        $key = $state instanceof LicenseStatus ? $state->value : $state;

        return $this->states()[$key];
    }
}
