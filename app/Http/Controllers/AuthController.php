<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterMensaController;
use App\Http\Requests\RegisterMensaRequest;
use App\Http\Requests\RegisterStudentController;
use App\Http\Requests\RegisterStudentRequest;
use App\Models\Canteen;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class AuthController extends Controller
{
    public function welcome(){
        return view('welcome');
    }
    public function showRegisterStudentForm(){
        return view('student.register');
    }

    public function registerStudent (RegisterStudentRequest $registerStudentRequest){

        $user = DB::transaction(function() use ($registerStudentRequest){
            $user = User::create(['name' =>$registerStudentRequest->name , 'username' => $registerStudentRequest->username , 'password' => $registerStudentRequest->password , 'email' => $registerStudentRequest->email , 'role' => UserRole::STUDENT]);
            $user->student()->create(['name' =>$registerStudentRequest->name , 'codice_fiscale' => $registerStudentRequest->username , 'matricola' => $registerStudentRequest->matricola ]);
            return $user ;
        });
       
        Auth::login($user);
        return redirect()->route('student.dashboard');
    }

    public function showRegisterMensaForm(){
        return view('admin.registermensa');
    }

    public function registerMensa(RegisterMensaRequest $registerMensaRequest){

        DB::transaction(function() use ($registerMensaRequest){
            $user = User::create(['name' =>$registerMensaRequest->name , 'username' => $registerMensaRequest->username , 'password' => $registerMensaRequest->password , 'email' => $registerMensaRequest->email , 'role' => UserRole::MENSA]);
            $user->Canteen()->create(['name' =>$registerMensaRequest->name , 'address' => $registerMensaRequest->address ]);
        });
        
        return back()->with(['success' => "you registered Mensa Successfully !"]);
    }



    public function showLoginForm(){
        return view('login');
    }

    public function login(LoginRequest $request)
    {
    
        if (!Auth::attempt($request->only('username', 'password'))) {
            return back()->withErrors(['username' => 'Username or password is wrong'])->onlyInput('username');
        }

        $user = Auth::user();


        return match ($user->role) {
            UserRole::ADMIN => redirect()->route('admin.dashboard')->with('success', "Welcome dear {$user->name}"),
            UserRole::MENSA => redirect()->route('mensa.dashboard')->with('success', "Welcome dear {$user->name}"),
            UserRole::STUDENT => redirect()->route('student.dashboard')->with('success', "Welcome dear {$user->name}"),
        };
    }

    public function studentDashboard(){
        return view('student.dashboard');
    }

    public function mensaDashboard(){
        return view('mensa.dashboard');
    }

    public function adminDashboard(){
        return view('admin.dashboard');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect()->route('welcome');
    }
}
