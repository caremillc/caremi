<?php declare (strict_types = 1);
namespace App\Repository;

use App\Entity\User;
use Careminate\Database\Dbal\EntityManager\DataMapper;

class UserMapper
{
    public function __construct(private DataMapper $dataMapper)
    {}

    public function emailExists(string $email): bool
    {
        $result = $this->dataMapper
            ->getConnection()
            ->createQueryBuilder()
            ->select('id')
            ->from('users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery();

        return (bool) $result->fetchOne();
    }

    public function save(User $user): void
    {
        if ($this->emailExists($user->getEmail())) {
            throw new \Exception('Email already exists');
        }

        $stmt = $this->dataMapper->getConnection()->prepare("
        INSERT INTO users (username, email, role, password, created_at)
        VALUES (:username, :email, :role, :password, :created_at)
    ");

        $stmt->bindValue(':username', $user->getUsername());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':role', $user->getRole());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':created_at', $user->getCreatedAt()->format('Y-m-d H:i:s'));

        $stmt->executeStatement();

        $id = (int) $this->dataMapper->getConnection()->lastInsertId();
        $user->setId($id);
    }

    public function findByEmail(string $email): ?User
    {
        $connection = $this->dataMapper->getConnection();

        $result = $connection->executeQuery(
            'SELECT * FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        );

        $row = $result->fetchAssociative();

        return $row ? new User($row) : null;
    }
    /**
     * Fetch all users
     *
     * @return User[]
     */
    public function findAll(): array
    {
        $connection = $this->dataMapper->getConnection();

        $result = $connection->executeQuery(
            'SELECT * FROM users ORDER BY id DESC'
        );

        $rows = $result->fetchAllAssociative();

        return array_map(
            fn(array $row) => new User($row),
            $rows
        );
    }

    public function findById(int $id): ?User
    {
        $connection = $this->dataMapper->getConnection();

        $result = $connection->executeQuery(
            'SELECT * FROM users WHERE id = :id LIMIT 1',
            ['id' => $id]
        );

        $row = $result->fetchAssociative();

        return $row ? new User($row) : null;
    }

    public function update(User $user): void
    {
        $connection = $this->dataMapper->getConnection();

        $affectedRows = $connection->executeStatement(
            'UPDATE users
         SET username = :username,
             email = :email,
             role = :role,
             password = :password,
             updated_at = :updated_at,
             version = version + 1
         WHERE id = :id
           AND version = :version',
            [
                'username'   => $user->getUsername(),
                'email'      => $user->getEmail(),
                'role'       => $user->getRole(),
                'password'   => $user->getPassword(),
                'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'id'         => $user->getId(),
                
            ]
        );

        if ($affectedRows === 0) {
            throw new \RuntimeException(
                'Optimistic lock failed. Record was modified by another transaction.'
            );
        }

    }

    public function delete(int $id): void
    {
        $connection = $this->dataMapper->getConnection();

        $connection->executeStatement(
            'DELETE FROM users WHERE id = :id',
            ['id' => $id]
        );
    }
}
