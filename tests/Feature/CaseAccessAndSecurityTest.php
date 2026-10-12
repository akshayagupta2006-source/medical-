<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CaseAccessAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement 6.3: Guests cannot access protected case pages or APIs.
     */
    public function test_guests_cannot_access_protected_case_web_pages(): void
    {
        $response = $this->get('/cases');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $responseCreate = $this->get('/cases/create');
        $responseCreate->assertStatus(302);
        $responseCreate->assertRedirect('/login');

        $responseDashboard = $this->get('/dashboard');
        $responseDashboard->assertStatus(302);
        $responseDashboard->assertRedirect('/login');
    }

    public function test_guests_cannot_access_protected_case_apis(): void
    {
        $response = $this->getJson('/api/cases');
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
            ]);

        $responsePost = $this->postJson('/api/cases', [
            'case_number' => 'CAS-99999',
            'patient_reference' => 'PAT-1111',
            'surgeon_name' => 'Dr. Stranger',
            'implant_type' => 'Spinal Cage',
            'status' => 'Draft',
            'priority' => 'Low',
        ]);
        $responsePost->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
            ]);
    }

    /**
     * Requirement 6.4: Admin can create a case (and created_by is set automatically).
     */
    public function test_admin_can_create_a_case_via_web(): void
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'case_number' => 'CAS-20261',
            'patient_reference' => 'PAT-7788',
            'surgeon_name' => 'Dr. Gregory House',
            'implant_type' => 'Titanium Hip Stem 12mm',
            'status' => MedicalCase::STATUS_PLANNING,
            'priority' => MedicalCase::PRIORITY_HIGH,
            'surgery_date' => '2026-11-15',
        ];

        $response = $this->actingAs($admin)->post('/cases', $payload);

        $response->assertStatus(302);
        $this->assertDatabaseHas('cases', [
            'case_number' => 'CAS-20261',
            'patient_reference' => 'PAT-7788',
            'surgeon_name' => 'Dr. Gregory House',
            'implant_type' => 'Titanium Hip Stem 12mm',
            'status' => MedicalCase::STATUS_PLANNING,
            'priority' => MedicalCase::PRIORITY_HIGH,
            'created_by' => $admin->id,
        ]);
    }

    public function test_admin_can_create_a_case_via_api(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $payload = [
            'case_number' => 'CAS-API-001',
            'patient_reference' => 'PAT-9900',
            'surgeon_name' => 'Dr. Meredith Grey',
            'implant_type' => 'Ceramic Acetabular Cup 48mm',
            'status' => MedicalCase::STATUS_APPROVED,
            'priority' => MedicalCase::PRIORITY_MEDIUM,
            'surgery_date' => '2026-12-01',
        ];

        $response = $this->postJson('/api/cases', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Medical case created successfully.',
                'data' => [
                    'case_number' => 'CAS-API-001',
                    'patient_reference' => 'PAT-9900',
                    'created_by' => $admin->id,
                ],
            ]);

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CAS-API-001',
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Requirement 6.5: Duplicate case number is rejected with 422.
     */
    public function test_duplicate_case_number_is_rejected_with_422(): void
    {
        $admin = User::factory()->admin()->create();
        MedicalCase::factory()->create([
            'case_number' => 'CAS-DUPLICATE-01',
            'created_by' => $admin->id,
        ]);

        Sanctum::actingAs($admin);

        $duplicatePayload = [
            'case_number' => 'CAS-DUPLICATE-01', // existing case number
            'patient_reference' => 'PAT-1234',
            'surgeon_name' => 'Dr. John Watson',
            'implant_type' => 'Knee Component',
            'status' => MedicalCase::STATUS_DRAFT,
            'priority' => MedicalCase::PRIORITY_LOW,
        ];

        $response = $this->postJson('/api/cases', $duplicatePayload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['case_number']);
    }

    /**
     * Requirement 6.6: Viewer cannot delete a case and receives 403.
     */
    public function test_viewer_cannot_delete_a_case_via_web_and_receives_403(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();

        $case = MedicalCase::factory()->create([
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($viewer)->delete("/cases/{$case->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
        ]);
    }

    public function test_viewer_cannot_delete_a_case_via_api_and_receives_403(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();

        $case = MedicalCase::factory()->create([
            'created_by' => $admin->id,
        ]);

        Sanctum::actingAs($viewer);

        $response = $this->deleteJson("/api/cases/{$case->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'data' => null,
            ]);

        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
        ]);
    }

    public function test_viewer_cannot_create_a_case_via_api_and_receives_403(): void
    {
        $viewer = User::factory()->viewer()->create();
        Sanctum::actingAs($viewer);

        $response = $this->postJson('/api/cases', [
            'case_number' => 'CAS-VIEWER-001',
            'patient_reference' => 'PAT-8888',
            'surgeon_name' => 'Dr. Derek Shepherd',
            'implant_type' => 'Mesh Plate',
            'status' => MedicalCase::STATUS_DRAFT,
            'priority' => MedicalCase::PRIORITY_LOW,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'data' => null,
            ]);
    }

    public function test_case_update_ignores_current_case_id_for_unique_case_number(): void
    {
        $admin = User::factory()->admin()->create();
        $case = MedicalCase::factory()->create([
            'case_number' => 'CAS-ORIGINAL-99',
            'created_by' => $admin->id,
        ]);

        Sanctum::actingAs($admin);

        $updatePayload = [
            'case_number' => 'CAS-ORIGINAL-99', // same case number
            'patient_reference' => 'PAT-NEWREF-11',
            'surgeon_name' => 'Dr. Cristina Yang',
            'implant_type' => 'Cardiac Valve 24mm',
            'status' => MedicalCase::STATUS_COMPLETED,
            'priority' => MedicalCase::PRIORITY_HIGH,
        ];

        $response = $this->putJson("/api/cases/{$case->id}", $updatePayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Medical case updated successfully.',
                'data' => [
                    'id' => $case->id,
                    'case_number' => 'CAS-ORIGINAL-99',
                    'patient_reference' => 'PAT-NEWREF-11',
                    'surgeon_name' => 'Dr. Cristina Yang',
                ],
            ]);
    }
}
