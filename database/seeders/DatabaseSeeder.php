<?php

namespace Database\Seeders;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@jajalmedical.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Password123!'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@jajalmedical.com'],
            [
                'name' => 'Clinical Viewer',
                'password' => Hash::make('Password123!'),
                'role' => User::ROLE_VIEWER,
                'email_verified_at' => now(),
            ]
        );

        $curatedCases = [
            [
                'case_number' => 'CAS-10001',
                'patient_reference' => 'PAT-8492',
                'surgeon_name' => 'Dr. Eleanor Vance',
                'implant_type' => 'Titanium Hip Stem 12mm',
                'status' => MedicalCase::STATUS_PLANNING,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(5)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CAS-10002',
                'patient_reference' => 'PAT-3941',
                'surgeon_name' => 'Dr. Robert Chen',
                'implant_type' => 'Ceramic Acetabular Cup 48mm',
                'status' => MedicalCase::STATUS_IN_REVIEW,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->addDays(12)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CAS-10003',
                'patient_reference' => 'PAT-1823',
                'surgeon_name' => 'Dr. Maya Patel',
                'implant_type' => 'Knee Femoral Component Left',
                'status' => MedicalCase::STATUS_APPROVED,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(3)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CAS-10004',
                'patient_reference' => 'PAT-9042',
                'surgeon_name' => 'Dr. Gregory House',
                'implant_type' => 'Spinal Fusion PEEK Cage L4-L5',
                'status' => MedicalCase::STATUS_COMPLETED,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => now()->subDays(7)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CAS-10005',
                'patient_reference' => 'PAT-6621',
                'surgeon_name' => 'Dr. Marcus Thorne',
                'implant_type' => 'Bipolar Hip Prosthesis 42mm',
                'status' => MedicalCase::STATUS_DRAFT,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->addDays(20)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CAS-10006',
                'patient_reference' => 'PAT-4410',
                'surgeon_name' => 'Dr. Sarah Connor',
                'implant_type' => 'Craniomaxillofacial Mesh 0.6mm',
                'status' => MedicalCase::STATUS_CANCELLED,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($curatedCases as $caseData) {
            MedicalCase::firstOrCreate(
                ['case_number' => $caseData['case_number']],
                $caseData
            );
        }

        // Generate 20 additional dummy cases to exceed the required 20 cases minimum
        MedicalCase::factory(20)->create([
            'created_by' => $admin->id,
        ]);
    }
}
