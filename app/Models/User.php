<?php 
namespace App\Models;

use Careminate\Databases\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
    protected $guarded = ['password']; // Explicitly guard password field

    // Automatically hash passwords when setting
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }
}