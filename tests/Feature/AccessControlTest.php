<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_admin_dashboard()
    {
        
        $studentUser = User::create([
            'name' => 'John Doe',
            'email' => 'student@unime.it',
            'username' => 'johndoe',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        
        $response = $this->actingAs($studentUser)->get('/ersu');
        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_welcome_page()
    {
    
        $response = $this->get('/student');
        $response->assertRedirect('/login');
    }
}