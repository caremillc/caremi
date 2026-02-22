<?php declare(strict_types=1);

use Careminate\Database\Blueprint\Blueprint;
use Careminate\Database\Migrations\Migration;
use Careminate\Database\Schema\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migration
     */
    public function up(): void
    {
        /** @var Schema $schema */
        $schema = $this->schema;
        
        $schema->create('users', function (Blueprint $table) {
            $table->increments('id'); // Primary key
            $table->string('username', 50)->nullable()->index();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migration
     */
    public function down(): void
    {
        $this->schema->dropIfExists('users');
    }
}

