<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Organization;
use App\Models\PluginSubmission;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/demo');
    }

    public function test_the_grades_module_is_available(): void
    {
        $user = User::factory()->create();
        $organization = Organization::create(['name' => 'Schule', 'slug' => 'schule']);
        $organization->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->get('/notenverwaltung');

        $response->assertOk()
            ->assertSee('Notenverwaltung')
            ->assertSee('Leistungen übersichtlich erfassen');
    }

    public function test_grades_are_saved_for_the_current_organization(): void
    {
        $user = User::factory()->create();
        $organization = Organization::create(['name' => 'Schule', 'slug' => 'schule']);
        $organization->users()->attach($user, ['role' => 'owner']);

        $this->actingAs($user)->post('/notenverwaltung/noten', [
            'student_name' => 'Erika Muster',
            'class_name' => '8a',
            'subject' => 'Mathematik',
            'grade' => '2.0',
            'assessment' => 'Klassenarbeit',
        ])->assertRedirect('/notenverwaltung');

        $this->assertDatabaseHas('grades', [
            'organization_id' => $organization->id,
            'student_name' => 'Erika Muster',
            'subject' => 'Mathematik',
        ]);
    }

    public function test_platform_navigation_pages_are_available(): void
    {
        foreach (['/support', '/einstellungen', '/team', '/module', '/entwickler'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_developer_can_submit_a_plugin_for_review(): void
    {
        $this->post('/entwickler/einreichung', [
            'developer_name' => 'Nexora Labs',
            'developer_email' => 'dev@example.test',
            'package_name' => 'vendor/catalog',
            'version' => '1.0.0',
            'description' => 'Ein Katalogmodul.',
            'plan_type' => 'subscription',
            'price_eur' => '9.90',
        ])->assertRedirect('/entwickler');

        $this->assertDatabaseHas('plugin_submissions', [
            'package_name' => 'vendor/catalog',
            'price_cents' => 990,
            'status' => 'submitted',
        ]);
    }

    public function test_team_invitation_is_saved_with_a_role(): void
    {
        $response = $this->post('/team/invitations', [
            'name' => 'Anna Müller',
            'email' => 'anna@example.test',
            'role' => 'editor',
        ]);

        $response->assertRedirect('/team');
        $this->assertDatabaseHas('team_members', [
            'email' => 'anna@example.test',
            'role' => 'editor',
            'status' => 'invited',
        ]);
    }

    public function test_only_platform_admins_can_open_customer_overview(): void
    {
        $this->actingAs(User::factory()->create(['is_platform_admin' => false]))
            ->get('/admin/kunden')
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['is_platform_admin' => true]))
            ->get('/admin/kunden')
            ->assertOk()
            ->assertSee('Kunden & Abos', false);

        $this->get('/admin/lizenzen')
            ->assertOk()
            ->assertSee('Lizenzen &amp; Einreichungen', false);
    }

    public function test_demo_and_customer_login_are_available(): void
    {
        $this->get('/demo')->assertOk()->assertSee('Interaktive Vorschau');
        $this->get('/login')->assertOk()->assertSee('Willkommen zurück.');
        $this->get('/registrieren')->assertOk()->assertSee('Starte mit Nexora.');
    }

    public function test_registration_creates_user_and_organization(): void
    {
        $this->post('/registrieren', [
            'name' => 'Max Mustermann',
            'organization' => 'Beispiel GmbH',
            'email' => 'max@example.test',
            'password' => 'sicheres-passwort',
            'password_confirmation' => 'sicheres-passwort',
        ])->assertRedirect('/kundenbereich');

        $this->assertAuthenticatedAs(User::where('email', 'max@example.test')->first());
        $this->assertDatabaseHas('organizations', [
            'name' => 'Beispiel GmbH',
            'slug' => 'beispiel-gmbh',
        ]);
        $this->assertDatabaseHas('organization_user', [
            'role' => 'owner',
        ]);
    }

    public function test_platform_admin_can_log_in_through_the_regular_login(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'admin-password',
            'is_platform_admin' => true,
        ]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'admin-password',
        ])->assertRedirect('/admin/kunden');

        $this->get('/admin/kunden')->assertOk();
    }

    public function test_public_information_pages_are_available(): void
    {
        $this->get('/dokumentation')->assertOk()->assertSee('Dokumentation');
        $this->get('/impressum')->assertOk()->assertSee('Impressum');
        $this->get('/datenschutz')->assertOk()->assertSee('Datenschutz');
    }

    public function test_demo_start_creates_an_isolated_demo_account(): void
    {
        $this->get('/demo/start')
            ->assertRedirect('/kundenbereich');

        $this->assertAuthenticatedAs(User::where('email', 'demo@nexora.local')->first());
        $this->assertDatabaseHas('organizations', ['slug' => 'nexora-demo']);
    }

    public function test_pricing_configuration_contains_published_business_rules(): void
    {
        $this->assertSame(15, config('pricing.cloud_monthly_eur'));
        $this->assertSame(50, config('pricing.self_hosted.base_one_time_eur'));
        $this->assertSame(45, config('pricing.self_hosted.threshold_one_time_eur'));
        $this->assertSame(100, config('pricing.self_hosted.customer_threshold'));
        $this->assertSame(10, config('pricing.developer_revenue_share_percent'));
    }

    public function test_customer_is_redirected_to_own_organization_area(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);
        $organization = Organization::create(['name' => 'Beispiel GmbH', 'slug' => 'beispiel-gmbh']);
        $organization->users()->attach($user, ['role' => 'owner']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect('/kundenbereich');

        $this->get('/kundenbereich')->assertOk()->assertSee('Beispiel GmbH');
    }
}
