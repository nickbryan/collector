<?php

declare(strict_types=1);

namespace Postgres\Membership\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210328182903 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create members table';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('members');

        $table->addColumn('id', 'bigint', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('uuid', 'guid', ['customSchemaOptions' => ['unique' => true]]);
        $table->addColumn('email', 'string', ['customSchemaOptions' => ['unique' => true]]);
        $table->addColumn('password', 'string');
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');

        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('members');
    }
}
