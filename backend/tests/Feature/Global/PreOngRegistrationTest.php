<?php

declare(strict_types=1);

namespace Tests\Feature\Global;

use App\Models\PreOngRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreOngRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_CNPJ = '11.222.333/0001-81';

    private function payload(array $overrides = []): array
    {
        return array_merge(['name' => 'Patas do Bem', 'email' => 'contato@patas.org', 'phone' => '(85) 99999-0000', 'city' => 'Fortaleza', 'state' => 'ce'], $overrides);
    }

    public function test_anyone_sends_a_pre_registration_without_login(): void
    {
        $this->postJson('/api/v1/ong-registrations', $this->payload(['description' => 'Resgatamos cães', 'cnpj' => self::VALID_CNPJ]))
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.status_label', 'Recebido');

        $record = PreOngRegistration::firstOrFail();
        $this->assertSame('85999990000', $record->phone);
        $this->assertSame('CE', $record->state);
        $this->assertSame('11222333000181', $record->cnpj);
    }

    public function test_the_response_never_exposes_personal_data(): void
    {
        $response = $this->postJson('/api/v1/ong-registrations', $this->payload())->assertCreated();

        foreach (['id', 'email', 'phone', 'name', 'cnpj'] as $key) {
            $this->assertArrayNotHasKey($key, $response->json('data'));
        }
    }

    public function test_validates_required_fields_and_formats(): void
    {
        $this->postJson('/api/v1/ong-registrations', [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'phone', 'city', 'state'], 'errors');
        $this->postJson('/api/v1/ong-registrations', $this->payload(['phone' => '123']))->assertUnprocessable()->assertJsonValidationErrors('phone', 'errors');
        $this->postJson('/api/v1/ong-registrations', $this->payload(['state' => 'CEA']))->assertUnprocessable()->assertJsonValidationErrors('state', 'errors');
        $this->postJson('/api/v1/ong-registrations', $this->payload(['cnpj' => '11.111.111/1111-11']))->assertUnprocessable()->assertJsonValidationErrors('cnpj', 'errors');
    }

    public function test_cnpj_is_optional_and_blank_becomes_null(): void
    {
        $this->postJson('/api/v1/ong-registrations', $this->payload(['cnpj' => '']))->assertCreated();

        $this->assertNull(PreOngRegistration::firstOrFail()->cnpj);
    }

    public function test_rejects_duplicates_of_email_phone_and_cnpj(): void
    {
        $this->postJson('/api/v1/ong-registrations', $this->payload(['cnpj' => self::VALID_CNPJ]))->assertCreated();

        $this->postJson('/api/v1/ong-registrations', $this->payload(['email' => 'outro@patas.org', 'phone' => '85988880000', 'cnpj' => self::VALID_CNPJ]))->assertUnprocessable()->assertJsonValidationErrors('cnpj', 'errors');
        $this->postJson('/api/v1/ong-registrations', $this->payload(['phone' => '85988880000']))->assertUnprocessable()->assertJsonValidationErrors('email', 'errors');
        $this->postJson('/api/v1/ong-registrations', $this->payload(['email' => 'outro@patas.org']))->assertUnprocessable()->assertJsonValidationErrors('phone', 'errors');
    }

    public function test_status_lookup_by_email_or_phone(): void
    {
        PreOngRegistration::create(['name' => 'Patas', 'email' => 'Contato@Patas.org', 'phone' => '85999990000', 'city' => 'Fortaleza', 'state' => 'CE', 'status' => 'contacted']);

        $this->getJson('/api/v1/ong-registrations/status?contact=contato@patas.org')->assertOk()->assertJsonPath('data.status', 'contacted')->assertJsonPath('data.status_label', 'Em contato');
        $this->getJson('/api/v1/ong-registrations/status?contact='.urlencode('(85) 99999-0000'))->assertOk()->assertJsonPath('data.status', 'contacted');
    }

    public function test_status_lookup_errors(): void
    {
        $this->getJson('/api/v1/ong-registrations/status')->assertUnprocessable();
        $this->getJson('/api/v1/ong-registrations/status?contact=abc')->assertUnprocessable()->assertJsonValidationErrors('contact', 'errors');
        $this->getJson('/api/v1/ong-registrations/status?contact=x@y')->assertUnprocessable();
        $this->getJson('/api/v1/ong-registrations/status?contact=nao@existe.com')->assertUnprocessable()->assertJsonValidationErrors('contact', 'errors');
    }
}
