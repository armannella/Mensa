<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_transfer_money_to_another_student()
    {
        $senderUser = User::create(['name' => 'Sender', 'email' => 's@test.com', 'username' => 'sender', 'password' => bcrypt('123'), 'role' => 'student']);
        $receiverUser = User::create(['name' => 'Receiver', 'email' => 'r@test.com', 'username' => 'receiver', 'password' => bcrypt('123'), 'role' => 'student']);

        $senderStudent = Student::create(['user_id' => $senderUser->id, 'codice_fiscale' => 'CF111', 'matricola' => 111, 'name' => 'Sender']);
        $receiverStudent = Student::create(['user_id' => $receiverUser->id, 'codice_fiscale' => 'CF222', 'matricola' => 222, 'name' => 'Receiver']);

        $senderWallet = Wallet::create(['student_id' => $senderStudent->id, 'balance' => 50.00]);
        $receiverWallet = Wallet::create(['student_id' => $receiverStudent->id, 'balance' => 0.00]);

        $response = $this->actingAs($senderUser)->post('/student/wallet/post', [
            'matricola' =>  (string) $receiverStudent->matricola,
            'amount' => 20.00
        ]);

        $response->assertSessionHas('success');
        
        
        $this->assertEquals(30.00, $senderWallet->fresh()->balance);
        
        $this->assertEquals(20.00, $receiverWallet->fresh()->balance);
        
        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $senderWallet->id,
            'amount' => 20.00,
            'type' => 'outcome'
        ]);
    }
}