<?php declare (strict_types = 1);
namespace App\Entity;


use Careminate\Authentication\Contracts\AuthUserInterface;
use Careminate\Database\Dbal\EntityManager\Entity;

class User extends Entity implements AuthUserInterface
{
    protected ?int $id = null;
    protected string $username;
    protected string $email;
    protected string $role;
    protected string $password;
    protected ?\DateTimeImmutable $createdAt = null;
    protected ?\DateTimeImmutable $updatedAt = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public static function create(string $username, string $email, string $role, string $plainPassword) : self
    {
        return new self([
            'username'  => $username,
            'email'     => $email,
            'role'      => $role,
            'password'  => password_hash($plainPassword, PASSWORD_DEFAULT),
            'createdAt' => new \DateTimeImmutable(),
        ]);
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthId(): int
    {
        if ($this->id === null) {
            throw new \UnexpectedValueException("Auth ID cannot be null");
        }
        return $this->id;
    }
    
    public function getAuthIdentifier(): int|string
    {
        return $this->id;
    }
        
    public function getAuthPassword(): string
    {
        return $this->password;
    }
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function getTableName(): string
    {
        return 'users';
    }

}
