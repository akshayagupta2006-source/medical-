# Requirements Traceability Matrix

This document maps requirements from the Jajal Medical specification directly to their implementation files in the codebase and their corresponding automated feature tests.

---

## Traceability Mapping Table

| Req # | Requirement Description | Implementation Code | Test Method / Verification |
| :--- | :--- | :--- | :--- |
| **R1** | **Role-Based Authorization & Prevention of Viewer Deletion (HTTP 403)**<br>Admin can manage cases; Viewer can only view. Attempted delete by Viewer must return HTTP 403. | • `app/Policies/MedicalCasePolicy.php` (`delete()` method checks `$user->isAdmin()`)<br>• `app/Http/Controllers/Web/CaseWebController.php` (`Gate::authorize('delete', $case)`)<br>• `app/Http/Controllers/Api/CaseApiController.php` (`destroy()` method checks `$request->user()->can('delete', $case)`)<br>• `bootstrap/app.php` (Formats `AccessDeniedHttpException` to 403 JSON) | • `Tests\Feature\CaseAccessAndSecurityTest::test_viewer_cannot_delete_a_case_via_web_and_receives_403()`<br>• `Tests\Feature\CaseAccessAndSecurityTest::test_viewer_cannot_delete_a_case_via_api_and_receives_403()`<br>• `Tests\Feature\CaseAccessAndSecurityTest::test_viewer_cannot_create_a_case_via_api_and_receives_403()` |
| **R2** | **Unique Case Number & Validation Error Handling (HTTP 422)**<br>`case_number` is required and unique. Duplicate case number must be rejected with 422. Update must ignore current ID. | • `database/migrations/2026_10_12_120000_create_cases_table.php` (`unique('case_number')`)<br>• `app/Http/Requests/StoreMedicalCaseRequest.php` (`'unique:cases,case_number'`)<br>• `app/Http/Requests/UpdateMedicalCaseRequest.php` (`Rule::unique('cases', 'case_number')->ignore($caseId)`)<br>• `bootstrap/app.php` (Catches `ValidationException` and renders 422 JSON with field errors) | • `Tests\Feature\CaseAccessAndSecurityTest::test_duplicate_case_number_is_rejected_with_422()`<br>• `Tests\Feature\CaseAccessAndSecurityTest::test_case_update_ignores_current_case_id_for_unique_case_number()` |
| **R3** | **Authentication & Guest Protection**<br>Login/logout via Web and Sanctum API. Unauthenticated requests to protected case routes must be rejected. | • `app/Http/Controllers/Web/AuthController.php`<br>• `app/Http/Controllers/Api/AuthController.php`<br>• `routes/web.php` (`middleware('auth')`)<br>• `routes/api.php` (`middleware('auth:sanctum')`)<br>• `bootstrap/app.php` (`AuthenticationException` rendered as 401 JSON) | • `Tests\Feature\AuthenticationTest::test_valid_web_login_succeeds()`<br>• `Tests\Feature\AuthenticationTest::test_valid_api_login_succeeds_and_returns_sanctum_token()`<br>• `Tests\Feature\AuthenticationTest::test_invalid_web_login_is_rejected()`<br>• `Tests\Feature\AuthenticationTest::test_invalid_api_login_is_rejected_with_401()`<br>• `Tests\Feature\CaseAccessAndSecurityTest::test_guests_cannot_access_protected_case_web_pages()`<br>• `Tests\Feature\CaseAccessAndSecurityTest::test_guests_cannot_access_protected_case_apis()` |
| **R4** | **Automatic Creator Association**<br>`created_by` must automatically be set to the authenticated user creating the case. | • `app/Http/Controllers/Web/CaseWebController.php` (`$validated['created_by'] = $request->user()->id;`)<br>• `app/Http/Controllers/Api/CaseApiController.php` (`$validated['created_by'] = $request->user()->id;`) | • `Tests\Feature\CaseAccessAndSecurityTest::test_admin_can_create_a_case_via_web()`<br>• `Tests\Feature\CaseAccessAndSecurityTest::test_admin_can_create_a_case_via_api()` |

---

## Detailed Traceability Walkthroughs

### 1. Viewer Deletion Prevention (Requirement 1 & 6.6)
1. **Requirement:** "A Viewer attempting to delete through a direct URL or API request must receive HTTP 403."
2. **Policy Enforcement:**
   In `app/Policies/MedicalCasePolicy.php`:
   ```php
   public function delete(User $user, MedicalCase $case): bool
   {
       return $user->isAdmin();
   }
   ```
3. **Controller Execution:**
   In `app/Http/Controllers/Api/CaseApiController.php`:
   ```php
   if (! $request->user()->can('delete', $case)) {
       return response()->json([
           'success' => false,
           'message' => 'This action is unauthorized.',
           'data' => null,
       ], 403);
   }
   ```
4. **Automated Verification:**
   In `tests/Feature/CaseAccessAndSecurityTest.php`:
   ```php
   public function test_viewer_cannot_delete_a_case_via_api_and_receives_403(): void
   {
       $admin = User::factory()->admin()->create();
       $viewer = User::factory()->viewer()->create();
       $case = MedicalCase::factory()->create(['created_by' => $admin->id]);

       Sanctum::actingAs($viewer);
       $response = $this->deleteJson("/api/cases/{$case->id}");

       $response->assertStatus(403);
       $this->assertDatabaseHas('cases', ['id' => $case->id]);
   }
   ```

---

### 2. Duplicate Case Number Rejection (Requirement 4 & 6.5)
1. **Requirement:** "Validate unique case number. Duplicate case number is rejected with 422."
2. **Validation Rule:**
   In `app/Http/Requests/StoreMedicalCaseRequest.php`:
   ```php
   'case_number' => ['required', 'string', 'max:50', 'unique:cases,case_number'],
   ```
3. **Database Guard:**
   In `database/migrations/2026_10_12_120000_create_cases_table.php`:
   ```php
   $table->string('case_number')->unique();
   ```
4. **Automated Verification:**
   In `tests/Feature/CaseAccessAndSecurityTest.php`:
   ```php
   public function test_duplicate_case_number_is_rejected_with_422(): void
   {
       $admin = User::factory()->admin()->create();
       MedicalCase::factory()->create(['case_number' => 'CAS-DUPLICATE-01', 'created_by' => $admin->id]);

       Sanctum::actingAs($admin);
       $response = $this->postJson('/api/cases', [
           'case_number' => 'CAS-DUPLICATE-01',
           'patient_reference' => 'PAT-1234',
           'surgeon_name' => 'Dr. John Watson',
           'implant_type' => 'Knee Component',
           'status' => 'Draft',
           'priority' => 'Low',
       ]);

       $response->assertStatus(422)
           ->assertJsonValidationErrors(['case_number']);
   }
   ```

---

### 3. Automatic `created_by` Association (Requirement 2 & 6.4)
1. **Requirement:** "Set created_by automatically to the authenticated user when creating a case."
2. **Controller Logic:**
   In `app/Http/Controllers/Api/CaseApiController.php`:
   ```php
   $validated = $request->validated();
   $validated['created_by'] = $request->user()->id;
   $case = MedicalCase::create($validated);
   ```
3. **Automated Verification:**
   In `tests/Feature/CaseAccessAndSecurityTest.php`:
   ```php
   $response = $this->postJson('/api/cases', $payload);
   $this->assertDatabaseHas('cases', [
       'case_number' => 'CAS-API-001',
       'created_by' => $admin->id,
   ]);
   ```
