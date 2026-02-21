<?php declare(strict_types=1);
namespace App\Repository;

use App\Entity\Post;
use Careminate\Exceptions\Http\NotFoundException;
use Doctrine\DBAL\Connection;

class PostRepository
{
    public function __construct(private Connection $connection)
    {}

    public function findById(int $id): ?Post
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('id', 'title', 'description', 'image', 'created_at', 'updated_at')
            ->from('posts')
            ->where('id = :id')
            ->setParameter('id', $id);

        $result = $queryBuilder->executeQuery();
        $row    = $result->fetchAssociative();

        if (! $row) {
            return null;
        }

        return new Post(
            (int) $row['id'],
            $row['title'],
            $row['description'],
            $row['image'] ?? null,
            isset($row['created_at']) ? new \DateTimeImmutable($row['created_at']) : new \DateTimeImmutable(),
            isset($row['updated_at']) ? new \DateTimeImmutable($row['updated_at']) : new \DateTimeImmutable()
        );
    }

    public function findOrFail(int $id): Post
    {
        $post = $this->findById($id);
        if (! $post) {
            throw new NotFoundException(sprintf('Post with ID %d not found', $id));
        }
        return $post;
    }

    public function update(Post $post): void
    {
        $this->connection->update('posts', [
            'title'       => $post->getTitle(),
            'description' => $post->getDescription(),
            'image'       => $post->getImage() ?? null,
            'created_at'  => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at'  => $post->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], [
            'id' => $post->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $affectedRows = $this->connection->delete('posts', [
            'id' => $id,
        ]);
        return $affectedRows > 0;
    }

    public function findAll(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('id', 'title', 'description', 'image', 'created_at', 'updated_at'
            )
            ->from('posts');

        $result = $queryBuilder->executeQuery();
        $rows   = $result->fetchAllAssociative();

        return array_map(
            fn($row) => new Post(
                (int) $row['id'],
                $row['title'],
                $row['description'],
                $row['image'] ?? null,
                isset($row['created_at']) ? new \DateTimeImmutable($row['created_at']) : new \DateTimeImmutable(),
                isset($row['updated_at']) ? new \DateTimeImmutable($row['updated_at']) : new \DateTimeImmutable()
            ),
            $rows
        );
    }

    public function all(string $tableName, array $columns = ['*'],  ? callable $transform = null) : array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(...$columns)
            ->from($tableName);

        $result = $queryBuilder->executeQuery();
        $rows   = $result->fetchAllAssociative();

        return $transform ? array_map($transform, $rows) : $rows;
    }

    public function findByTitle(string $title): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('id', 'title', 'description', 'image', 'created_at', 'updated_at')
            ->from('posts')
            ->where('title LIKE :title')
            ->setParameter('title', '%' . $title . '%');

        $result = $queryBuilder->executeQuery();
        $rows   = $result->fetchAllAssociative();

        return array_map(
            fn($row) => new Post(
                (int) $row['id'],
                $row['title'],
                $row['description'],
                $row['image'] ?? null,
                new \DateTimeImmutable($row['created_at']),
                new \DateTimeImmutable($row['updated_at'])
            ),
            $rows
        );
    }

    public function paginate(int $limit, int $offset): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('id', 'title', 'description', 'image', 'created_at', 'updated_at')
            ->from('posts')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $rows = $qb->executeQuery()->fetchAllAssociative();

        return array_map(fn($row) => new Post(
            (int) $row['id'],
            $row['title'],
            $row['description'],
            $row['image'] ?? null,
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        ), $rows);
    }

    public function count(): int
    {
        return (int) $this->connection
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('posts')
            ->executeQuery()
            ->fetchOne();
    }

}
