<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Uuid\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Uuid3
{
    public function __construct(
        private string|UuidInterface $namespace,
        private string $name,
        private string $field = 'uuid',
        private bool $nullable = false
    ) {
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if (!$this->nullable && !isset($event->state->getData()[$this->field])) {
            $event->state->register($this->field, Uuid::uuid3($this->namespace, $this->name));
        }
    }
}
