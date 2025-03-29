<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Uuid;

use Cycle\ORM\Entity\Behavior\Uuid\Listener\Uuid3 as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\UuidInterface;

/**
 * Uses a version 3 (name-based) UUID based on the MD5 hash of a
 * namespace ID and a name
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Uuid3 extends Uuid
{
    /**
     * @param string|UuidInterface $namespace The namespace (must be a valid UUID)
     * @param non-empty-string $name The name to use for creating a UUID
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param bool $nullable Indicates whether to generate a new UUID or not
     *
     * @see \Ramsey\Uuid\UuidFactoryInterface::uuid3()
     */
    public function __construct(
        private string|UuidInterface $namespace,
        private string $name,
        string $field = 'uuid',
        ?string $column = null,
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

    #[ArrayShape(['field' => 'string', 'namespace' => 'string', 'name' => 'string', 'nullable' => 'bool'])]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'namespace' => $this->namespace instanceof UuidInterface ? (string) $this->namespace : $this->namespace,
            'name' => $this->name,
            'nullable' => $this->nullable
        ];
    }
}
