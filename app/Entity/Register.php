<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserMapper;

class Register
{
    private string $username,$email,$role,$password;
    private array $errors = [];

    public function __construct(private UserMapper $userMapper){}

    public function setFields(string $username, string $email,string $role,  string $password): void
    {
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
    }

    public function save(): User
    {
        if ($this->hasValidationErrors()) {
            throw new \Exception('Validation errors: ' . implode(', ', $this->getValidationErrors()));
        }

        // Create user with the password and store it in the database
        $user = User::create($this->username, $this->email, $this->role, $this->password);
        $this->userMapper->save($user);

        return $user;
    }


    public function hasValidationErrors(): bool
    {
        return !empty($this->getValidationErrors());
    }

    public function getValidationErrors(): array
    {
        if (empty($this->errors)) {
            $this->validateUsername();
            $this->validateEmail();
            $this->validatePassword();
            $this->validateRole();
            $this->validateEmailFormat();
        }
        return $this->errors;
    }

    private function validateUsername(): void
    {
        if (strlen($this->username) < 5 || strlen($this->username) > 20) {
            $this->errors[] = 'Username must be between 5 and 20 characters';
        }

        if (!preg_match('/^\w+$/', $this->username)) {
            $this->errors[] = 'Username can only consist of word characters without spaces';
        }
    }

    private function validateEmail(): void
    {
        // Trim email to avoid validation issues with accidental spaces
        $email = trim($this->email);

        //Check if email is empty or not a valid email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Invalid email address';
        }
       
    }

    private function validateRole(): void
    {
        if (empty($this->role)) {
            $this->errors[] = 'Role is required';
        }
    }

    private function validateEmailFormat(): void
    {
        // Trim email to avoid validation issues with accidental spaces
        $email = trim($this->email);

        //Check if email is empty or not a valid email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Invalid email address';
        }
       
    }
    private function validatePassword(): void
    {
        $this->validatePasswordLength();
        $this->validatePasswordComplexity();
        $this->validateCommonPasswords();
        $this->validatePasswordAgainstUsername();
    }

    private function validatePasswordLength(): void
    {
        if (strlen($this->password) < 8) {
            $this->errors[] = 'Password must be at least 8 characters long';
        }
    }

    private function validatePasswordComplexity(): void
    {
        if (!preg_match('/[A-Z]/', $this->password)) {
            $this->errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $this->password)) {
            $this->errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/\d/', $this->password)) {
            $this->errors[] = 'Password must contain at least one digit';
        }

        if (!preg_match('/[\W_]/', $this->password)) {
            $this->errors[] = 'Password must contain at least one special character';
        }
    }

    private function validateCommonPasswords(): void
    {
        $commonPasswords = array_map('strtolower', [
            'password', '123456', '123456789', '12345678', '12345', '1234567',
            'qwerty', 'abc123', 'password1', '111111', '123123', 'admin'
        ]);

        if (in_array(strtolower($this->password), $commonPasswords, true)) {
            $this->errors[] = 'Password is too common';
        }
    }

    private function validatePasswordAgainstUsername(): void
    {
        if (strtolower($this->password) === strtolower($this->username)) {
            $this->errors[] = 'Password cannot be the same as the username';
        }
    }
}
