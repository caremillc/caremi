<?php declare(strict_types=1);

use Careminate\Database\Blueprint\Blueprint;
use Careminate\Database\Migrations\Migration;
use Careminate\Database\Schema\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migration
     */
    public function up(): void
    {
        /** @var Schema $schema */
        $schema = $this->schema;
        
        $schema->create('posts', function (Blueprint $table) {
            $table->increments('id'); // Primary key
            $table->string('username', 255)->nullable()->index();
            $table->string('description', 255);
            $table->string('image');
            $table->timestamps('created_at');
            $table->timestamps('updated_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migration
     */
    public function down(): void
    {
        $this->schema->dropIfExists('posts');
    }
}



