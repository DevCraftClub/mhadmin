<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultF7929535b3e4d171fee5df7e2c079240 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('devcraft_logs')
        ->alterIndex(['uuid'], ['name' => 'dle_devcraft_logs_index_uuid_6a2308ea4719e', 'unique' => true])
        ->update();
    }

    public function down(): void
    {
        $this->table('devcraft_logs')
        ->alterIndex(['uuid'], ['name' => 'dle_devcraft_logs_index_uuid_6a2308ea4719e', 'unique' => false])
        ->update();
    }
}
