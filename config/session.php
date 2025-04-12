<?php
return [
    'session_save_path'=>base_path('storage/sessions'),
    'expiration_timeout'=> 3600,

    'encryption_mode'=>config('app.cipher'),
    'encryption_key'=>config('app.key'),
    
    'session_driver'=>'file', // file|database 
    'session_table' => 'sessions', // to save session in the database
    'session_prefix'=>'caremi',
];
