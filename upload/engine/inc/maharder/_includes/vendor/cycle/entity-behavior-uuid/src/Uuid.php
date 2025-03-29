<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Uuid;

use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Ramsey\Uuid\Uuid as RamseyUuid;

abstract class Uuid extends BaseModifier
{
    protected ?string $column = null;
    protected string $field;
    protected bool $nullable = false;

    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        $this->column = $modifier->findColumnName($this->field, $this->column);
        if ($this->column !== null) {
            $modifier->addUuidColumn(
                $this->column,
                $this->field,
                $this->nullable ? null : GeneratedField::BEFORE_INSERT
            )->nullable($this->nullable);
            $modifier->setTypecast(
                $registry->getEntity($this->role)->getFields()->get($this->field),
                [RamseyUuid::class, 'fromString']
            );
        }
    }

    public function render(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        $this->column = $modifier->findColumnName($this->field, $this->column) ?? $this->field;

        $modifier->addUuidColumn(
            $this->column,
            $this->field,
            $this->nullable ? null : GeneratedField::BEFORE_INSERT
        )->nullable($this->nullable);
        $modifier->setTypecast(
            $registry->getEntity($this->role)->getFields()->get($this->field),
            [RamseyUuid::class, 'fromString']
        );
    }
}
