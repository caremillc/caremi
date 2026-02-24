<?php declare (strict_types = 1);

namespace App\Repository;

use App\Entity\User;
use Careminate\Authentication\Contracts\AuthRepositoryInterface;
use Doctrine\DBAL\Connection;

class UserRepository implements AuthRepositoryInterface
{
    public function __construct(private Connection $connection)
    {}

    // ------------------------
    // Interface Methods
    // ------------------------
    public function findById(int | string $id): ?User
    {
        return $this->findOne(['id' => $id]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOne(['email' => $email]);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->findOne(['username' => $username]);
    }

    public function findByIdentifier(string $identifier): ?User
    {
        $row = $this->connection->createQueryBuilder()
            ->select('id', 'username', 'email', 'password')
            ->from('users')
            ->where('email = :id OR username = :id')
            ->setParameter('id', $identifier)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->hydrateUser($row) : null;
    }
    // ------------------------
    // CRUD Methods
    // ------------------------
    public function save(User $user): void
    {
        $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);

        $this->connection->insert('users', [
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'password' => $hashedPassword            
        ]);

        $user->setId((int) $this->connection->lastInsertId());
        $user->setPassword($hashedPassword); // Keep entity consistent

    }

    public function update(User $user): void
    {

        $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);

        $affectedRows = $this->connection->update(
            'users',
            [
                'username' => $user->getUsername(),
                'email'    => $user->getEmail(),
                'password' => $hashedPassword,

            ],
            [
                'id' => $user->getId(),

            ]
        );

        if ($affectedRows === 0) {
            throw new \RuntimeException('Update failed: version mismatch (optimistic locking)');
        }

        $user->setPassword($hashedPassword);

    }

    public function delete(User $user): void
    {
        // Example safe delete: check constraints or logical delete
        $affectedRows = $this->connection->delete('users', ['id' => $user->getId()]);

        if ($affectedRows === 0) {
            throw new \RuntimeException('Delete failed: user not found');
        }
    }

    // ------------------------
    // Helper Methods / DataMapper
    // ------------------------
    private function findOne(array $criteria): ?User
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('id', 'username', 'email', 'password')
            ->from('users');

        foreach ($criteria as $field => $value) {
            $qb->andWhere("$field = :$field")
                ->setParameter($field, $value);
        }

        $row = $qb->executeQuery()->fetchAssociative();

        return $row ? $this->hydrateUser($row) : null;
    }

    private function hydrateUser(array $data): User
    {
        $user = new User();
        $user->setId((int) $data['id']);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        return $user;
    }
}
