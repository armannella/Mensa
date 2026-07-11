<?php

namespace App\Providers;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Reserve;
use App\Models\Student;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::define('isMenuForTheCanteen' ,function(User $user ,Menu $menu , Canteen $canteen){
            if($menu->canteen_id != $canteen->id){
                return false;
            }
            return true ;
        }) ;

        Gate::define('studentAlreadyReserved' ,function(User $user ,Menu $menu , Student $student){
            if($student->reserves()->where('menu_id' , $menu->id)->exists()){
                return true;
            }
            
            return false ;
        }) ;

         Gate::define('studentAlreadyReservedAnotherCanteen' ,function(User $user ,Menu $menu , Student $student){
            if($student->reserves()->where('date' , $menu->date)->where('meal' , $menu->meal)->exists()){
                return true;
            }
            
            return false ;
        }) ;


        

       
    }
}
