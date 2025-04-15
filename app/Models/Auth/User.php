<?php
namespace App\Models\Auth;

use App\Observers\UserObserver;
use Careminate\Databases\Model;

class User extends Model
{
    
    protected $table = 'users';
    
    // Define your model properties and methods
    protected static function booted()
    {
        static::observe(UserObserver::class);
    }
}