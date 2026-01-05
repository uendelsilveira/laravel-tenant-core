<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'central';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('central')->create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('domain');
            $table->index(['tenant_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('domains');
    }
};
