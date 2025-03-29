<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Uuid\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

final class Uuid1
{
    public function __construct(
        private string $field = 'uuid',
        private Hexadecimal|int|string|null $node = null,
        private ?int $clockSeq = null,
        private bool $nullable = false
    ) {
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if (!$this->nullable && !isset($event->state->getData()[$this->field])) {
            $event->state->register($this->field, Uuid::uuid1($this->node, $this->clockSeq));
        }
    }
}
