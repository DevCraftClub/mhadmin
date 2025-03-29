<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Uuid;

use Cycle\ORM\Entity\Behavior\Uuid\Listener\Uuid6 as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\Type\Hexadecimal;

/**
 * Uses a version 6 (ordered-time) UUID from a host ID, sequence number,
 * and the current time
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Uuid6 extends Uuid
{
    /**
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param Hexadecimal|string|null $node A 48-bit number representing the hardware
     * @param int|null $clockSeq A 14-bit number used to help avoid duplicates
     *     that could arise when the clock is set backwards in time or if the
     *     node ID changes
     * @param bool $nullable Indicates whether to generate a new UUID or not
     *
     * @see \Ramsey\Uuid\UuidFactoryInterface::uuid6()
     */
    public function __construct(
        string $field = 'uuid',
        ?string $column = null,
        private Hexadecimal|string|null $node = null,
        private ?int $clockSeq = null,
        bool $nullable = false
    ) {
        $this->field = $field;
        $this->column = $column;
        $this->nullable = $nullable;
    }

    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    #[ArrayShape(['field' => 'string', 'node' => 'string|null', 'clockSeq' => 'int|null', 'nullable' => 'bool'])]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'node' => $this->node instanceof Hexadecimal ? (string) $this->node : $this->node,
            'clockSeq' => $this->clockSeq,
            'nullable' => $this->nullable
        ];
    }
}
