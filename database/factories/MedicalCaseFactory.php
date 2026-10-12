<?php

namespace Database\Factories;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalCase>
 */
class MedicalCaseFactory extends Factory
{
    protected $model = MedicalCase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'case_number' => 'CAS-' . fake()->unique()->numberBetween(10000, 99999),
            'patient_reference' => 'PAT-' . fake()->numberBetween(1000, 9999),
            'surgeon_name' => 'Dr. ' . fake()->lastName(),
            'implant_type' => fake()->randomElement([
                'Titanium Hip Stem 12mm',
                'Ceramic Acetabular Cup 48mm',
                'Knee Femoral Component Left',
                'Spinal Fusion PEEK Cage L4-L5',
                'Shoulder Glenoid Component Reverse',
                'Bipolar Hip Prosthesis 42mm',
                'Craniomaxillofacial Mesh 0.6mm',
            ]),
            'status' => fake()->randomElement(MedicalCase::STATUSES),
            'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
            'surgery_date' => fake()->dateTimeBetween('-1 month', '+3 months')->format('Y-m-d'),
            'created_by' => User::factory(),
        ];
    }
}
