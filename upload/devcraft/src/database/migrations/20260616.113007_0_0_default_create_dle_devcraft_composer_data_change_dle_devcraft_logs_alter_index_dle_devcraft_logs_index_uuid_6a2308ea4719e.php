<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultFb7352ed1d0cefc5e46ccdbe00e7ec2b extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('devcraft_composer_data')
        ->addColumn('plugin', 'string', [
            'nullable' => false,
            'defaultValue' => null,
            'size' => 255,
            'comment' => '',
            'charset' => '',
            'collation' => '',
        ])
        ->addColumn('package', 'string', [
            'nullable' => false,
            'defaultValue' => null,
            'size' => 255,
            'comment' => '',
            'charset' => '',
            'collation' => '',
        ])
        ->addColumn('version', 'string', [
            'nullable' => false,
            'defaultValue' => null,
            'size' => 255,
            'comment' => '',
            'charset' => '',
            'collation' => '',
        ])
        ->addColumn('app_code', 'string', [
            'nullable' => false,
            'defaultValue' => null,
            'size' => 255,
            'comment' => '',
            'charset' => '',
            'collation' => '',
        ])
        ->addColumn('installed', 'boolean', [
            'nullable' => false,
            'defaultValue' => null,
            'size' => 1,
            'autoIncrement' => false,
            'unsigned' => false,
            'zerofill' => false,
            'comment' => '',
        ])
        ->addColumn('required', 'boolean', [
            'nullable' => false,
            'defaultValue' => null,
            'size' => 1,
            'autoIncrement' => false,
            'unsigned' => false,
            'zerofill' => false,
            'comment' => '',
        ])
        ->addColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => 'CURRENT_TIMESTAMP', 'comment' => ''])
        ->addColumn('creator', 'bigInteger', [
            'nullable' => true,
            'defaultValue' => null,
            'size' => 20,
            'autoIncrement' => false,
            'unsigned' => false,
            'zerofill' => false,
            'comment' => '',
        ])
        ->addColumn('last_editor', 'bigInteger', [
            'nullable' => true,
            'defaultValue' => null,
            'size' => 20,
            'autoIncrement' => false,
            'unsigned' => false,
            'zerofill' => false,
            'comment' => '',
        ])
        ->addColumn('updated_at', 'datetime', ['nullable' => true, 'defaultValue' => null, 'comment' => ''])
        ->addColumn('id', 'bigPrimary', [
            'nullable' => false,
            'defaultValue' => null,
            'autoincrement' => true,
            'size' => 20,
            'autoIncrement' => true,
            'unsigned' => false,
            'zerofill' => false,
            'comment' => '',
        ])
        ->setPrimaryKeys(['id'])
        ->create();

    }

    public function down(): void
    {

        $this->table('devcraft_composer_data')->drop();
    }
}
