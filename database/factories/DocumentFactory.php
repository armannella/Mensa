<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_type_id' => \App\Models\DocumentType::factory(),
            'file_path' => 'documents/students/' . fake()->uuid() . '.pdf',
            'student_id' => \App\Models\Student::factory(),
        ];
    }
}
