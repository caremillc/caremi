<?php
namespace App\Models;

use Careminate\Databases\Model;
use App\Support\Pagination\Paginator;
use Careminate\Databases\QueryBuilder\QueryBuilder;


class Post extends Model
{
    protected static string $table = 'posts';
    protected array $fillable = ['title', 'content', 'author_id'];

    public static function paginate(int $perPage = 15): Paginator
    {
        $query = new QueryBuilder(static::$table);
        $total = $query->count();
        $currentPage = (int) ($_GET['page'] ?? 1);
        
        $results = $query
            ->select()
            ->limit($perPage)
            ->offset(($currentPage - 1) * $perPage)
            ->get();

        return new Paginator(
            $results,
            $total,
            $perPage,
            $currentPage
        );
    }
}
