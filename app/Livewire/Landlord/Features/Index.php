<?php

namespace App\Livewire\Landlord\Features;

use App\Models\Landlord\Feature;
use App\Modules\Entitlements\Actions\CreateFeatureAction;
use App\Modules\Entitlements\Enums\FeatureStatus;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public string $key = '';

    public string $name = '';

    public string $description = '';

    public string $status = 'active';

    public function createFeature(CreateFeatureAction $action): void
    {
        $this->validate([
            'key' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z0-9_.-]+$/', 'unique:features,key'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::enum(FeatureStatus::class)],
        ]);

        $feature = $action->handle(
            key: $this->key,
            name: $this->name,
            description: $this->description !== '' ? $this->description : null,
            status: FeatureStatus::from($this->status),
            actor: auth('landlord')->user(),
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        session()->flash('success', 'Feature created.');

        $this->redirectRoute('landlord.features.show', $feature, navigate: true);
    }

    public function render()
    {
        return view('livewire.landlord.features.index', [
            'features' => Feature::query()
                ->withCount('tenantFeatures')
                ->orderBy('key')
                ->paginate(20),
        ]);
    }
}
