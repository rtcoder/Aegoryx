<?php

namespace Tests\Feature\Crm;

use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\User;
use App\Modules\Audit\Enums\ActivityEntryAction;
use App\Modules\Crm\Actions\CreateCompanyAction;
use App\Modules\Crm\Actions\CreateContactAction;
use App\Modules\Crm\Actions\CreateNoteAction;
use App\Modules\Crm\Actions\UpdateContactAction;
use App\Modules\Crm\Enums\CrmSubjectType;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CrmActivityEntriesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_crm_actions_record_actor_subject_and_redact_sensitive_payloads(): void
    {
        $actor = $this->user();

        $contact = app(CreateContactAction::class)->handle([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.test',
            'phone' => '+48 123 123 123',
        ], $actor);

        app(UpdateContactAction::class)->handle($contact, [
            'first_name' => 'Ada',
            'last_name' => 'Byron',
            'email' => 'ada.byron@example.test',
            'phone' => '+48 999 999 999',
        ], $actor);

        $company = app(CreateCompanyAction::class)->handle([
            'name' => 'Acme Corp',
            'email' => 'sales@example.test',
            'phone' => '+48 111 111 111',
        ], $actor);

        app(CreateNoteAction::class)->handle([
            'subject_type' => CrmSubjectType::Company->value,
            'subject_id' => $company->id,
            'body' => 'Private negotiation details.',
            'is_sensitive' => true,
        ], $actor);

        $contactCreated = ActivityEntry::query()
            ->where('action', ActivityEntryAction::CrmContactCreated)
            ->firstOrFail();
        $contactUpdated = ActivityEntry::query()
            ->where('action', ActivityEntryAction::CrmContactUpdated)
            ->firstOrFail();
        $companyCreated = ActivityEntry::query()
            ->where('action', ActivityEntryAction::CrmCompanyCreated)
            ->firstOrFail();
        $noteCreated = ActivityEntry::query()
            ->where('action', ActivityEntryAction::CrmNoteCreated)
            ->firstOrFail();

        $this->assertSame(User::class, $contactCreated->actor_type);
        $this->assertSame($actor->id, $contactCreated->actor_id);
        $this->assertSame(CrmContact::class, $contactCreated->subject_type);
        $this->assertSame($contact->id, $contactCreated->subject_id);
        $this->assertSame('[redacted]', $contactCreated->after_json['email']);
        $this->assertSame('[redacted]', $contactUpdated->before_json['phone']);
        $this->assertSame(CrmCompany::class, $companyCreated->subject_type);
        $this->assertSame('[redacted]', $companyCreated->after_json['email']);
        $this->assertSame('[redacted]', $noteCreated->after_json['body']);
    }

    private function user(): User
    {
        return User::query()->create([
            'name' => 'Tenant User',
            'email' => 'tenant@example.test',
            'password' => 'secret-password',
            'locale' => Locale::Polish,
        ]);
    }
}
