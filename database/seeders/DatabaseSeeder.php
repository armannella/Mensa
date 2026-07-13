<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'ERSU ADMIN',
            'email' => 'admin@ersu.it',
            'username' => 'ersu',
            'password' => '12345678', 
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Arman Khademi',
            'email' => 'arman.khademi@studenti.unime.it',
            'username' => 'armannella',
            'password' => '12345678', 
            'role' => 'student',
        ]);

        Student::create([
            'name' => 'Arman Khademi' ,
            'codice_fiscale' => 'armannella' ,
            'user_id' => 2 ,
            'matricola' => '556026' ]
        );

        Wallet::create([
            'student_id' => 1
        ]);

        $this->call([
            ConfigSeeder::class,
        ]);

        
    }
}
