<?php 

use Careminate\Database\DB;
   // testing 

    // Using helper functions
$debug = env('APP_DEBUG', false);
$dbConfig = config('database.connections.mysql');

// Using DB helper
DB::init(); // Auto-initializes from container

// Query examples
$users = DB::select('SELECT * FROM users WHERE active = ?', [1]);
$user = DB::selectOne('SELECT * FROM users WHERE id = ?', [1]);
DB::insert('users', ['name' => 'John', 'email' => 'john@example.com']);
DB::update('users', ['name' => 'Jane'], ['id' => 1]);
DB::delete('users', ['id' => 1]);

// Transactions
DB::beginTransaction();
try {
    DB::insert('users', ['name' => 'Bob']);
    DB::insert('profiles', ['user_id' => DB::connection()->lastInsertId()]);
    DB::commit();
} catch (Exception $e) {
    DB::rollback();
}

// Query Builder
$qb = DB::query('users')
    ->select('*')
    ->where('active = :active')
    ->setParameter('active', 1)
    ->orderBy('created_at', 'DESC')
    ->setMaxResults(10);
    
$results = $qb->fetchAllAssociative();

// end testing