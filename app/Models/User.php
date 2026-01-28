<?php declare(strict_types=1);
namespace App\Models;

use Careminate\Database\Model;

class User extends Model 
{
    //protected string $table = 'users'; // optional, defaults to class name + 's'
    protected array $fillable = ['name','email','password','status','role','deleted_at','created_at','updated_at'];
    
     // Enable/disable timestamps & soft deletes if needed
    protected bool $timestamps = true;
    protected bool $softDelete = true;

    // public function profile() { return $this->hasOne(Profile::class); }
    // public function posts() { return $this->hasMany(Post::class); }
    // public function roles() { return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id'); }
}