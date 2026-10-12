<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RolePermissionsVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_has_full_web_access(): void
    {
        $admin = User::factory()->admin()->create();
        $case = MedicalCase::factory()->create(['created_by' => $admin->id]);

        // Can access create page
        $this->actingAs($admin)->get('/cases/create')->assertStatus(200);

        // Can access edit page
        $this->actingAs($admin)->get("/cases/{$case->id}/edit")->assertStatus(200);

        // Can update case
        $this->actingAs($admin)->put("/cases/{$case->id}", [
            'case_number' => $case->case_number,
            'patient_reference' => 'PAT-8888',
            'surgeon_name' => 'Dr. Admin Lead',
            'implant_type' => 'Titanium Plate',
            'status' => 'In Review',
            'priority' => 'High',
        ])->assertStatus(302)->assertRedirect("/cases/{$case->id}");

        // Can delete case
        $this->actingAs($admin)->delete("/cases/{$case->id}")
            ->assertStatus(302)->assertRedirect('/cases');
    }

    public function test_viewer_is_strictly_read_only_on_web(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();
        $case = MedicalCase::factory()->create(['created_by' => $admin->id]);

        // Viewer CAN view case index and show
        $this->actingAs($viewer)->get('/cases')->assertStatus(200);
        $this->actingAs($viewer)->get("/cases/{$case->id}")->assertStatus(200);

        // Viewer CANNOT access create page (403)
        $this->actingAs($viewer)->get('/cases/create')->assertStatus(403);

        // Viewer CANNOT submit create (403)
        $this->actingAs($viewer)->post('/cases', [
            'case_number' => 'CAS-ILLEGAL-01',
            'patient_reference' => 'PAT-0001',
            'surgeon_name' => 'Dr. Unauthorized',
            'implant_type' => 'Illegal Mesh',
            'status' => 'Draft',
            'priority' => 'Low',
        ])->assertStatus(403);

        // Viewer CANNOT access edit page (403)
        $this->actingAs($viewer)->get("/cases/{$case->id}/edit")->assertStatus(403);

        // Viewer CANNOT submit update (403)
        $this->actingAs($viewer)->put("/cases/{$case->id}", [
            'case_number' => $case->case_number,
            'patient_reference' => 'PAT-MODIFIED',
            'surgeon_name' => 'Dr. Modified',
            'implant_type' => 'Modified Implant',
            'status' => 'Completed',
            'priority' => 'Low',
        ])->assertStatus(403);

        // Viewer CANNOT submit delete (403)
        $this->actingAs($viewer)->delete("/cases/{$case->id}")->assertStatus(403);
    }

    public function test_viewer_is_strictly_read_only_on_api(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();
        $case = MedicalCase::factory()->create(['created_by' => $admin->id]);

        Sanctum::actingAs($viewer);

        // Viewer CAN read via API
        $this->getJson('/api/cases')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson("/api/cases/{$case->id}")->assertStatus(200)->assertJson(['success' => true]);

        // Viewer CANNOT create via API (403)
        $this->postJson('/api/cases', [
            'case_number' => 'CAS-API-ILLEGAL',
            'patient_reference' => 'PAT-9999',
            'surgeon_name' => 'Dr. Hacker',
            'implant_type' => 'Unauthorized',
            'status' => 'Draft',
            'priority' => 'Low',
        ])->assertStatus(403)->assertJson(['success' => false, 'message' => 'This action is unauthorized.']);

        // Viewer CANNOT update via API (403)
        $this->putJson("/api/cases/{$case->id}", [
            'case_number' => $case->case_number,
            'patient_reference' => 'PAT-API-MOD',
            'surgeon_name' => 'Dr. Mod',
            'implant_type' => 'Mod Implant',
            'status' => 'Completed',
            'priority' => 'High',
        ])->assertStatus(403)->assertJson(['success' => false, 'message' => 'This action is unauthorized.']);

        // Viewer CANNOT delete via API (403)
        $this->deleteJson("/api/cases/{$case->id}")
            ->assertStatus(403)->assertJson(['success' => false, 'message' => 'This action is unauthorized.']);
    }
}
