<?php

declare(strict_types=1);

namespace Cycle\Schema\Table;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Exception\ColumnException;

/**
 * Carries information about column definition.
 *
 * @internal
 */
final class Column
{
    // default column value
    public const OPT_DEFAULT = 'default';

    // column can automatically define default value
    public const OPT_CAST_DEFAULT = 'castDefault';

    // column can be nullable
    public const OPT_NULLABLE = 'nullable';

    // provides ability to define complex types using string notation, i.e. string(32)
    private const DEFINITION = '/(?P<type>[a-z]+)(?: *\((?P<options>[^\)]+)\))?/i';

    /** @var Field */
    private $field;

    /** @var string */
    private $type;

    /** @var array */
    private $typeOptions = [];

    /**
     * Parse field definition into table definition.
     *
     * @throws ColumnException
     *
     */
    public static function parse(Field $field): self
    {
        $column = new self();
        $column->field = $field;

        if (!preg_match(self::DEFINITION, $field->getType(), $type)) {
            throw new ColumnException("Invalid column type declaration in `{$field->getType()}`");
        }

        $column->type = $type['type'];
        if (!empty($type['options'])) {
            $column->typeOptions = array_map('trim', explode(',', $type['options'] ?? ''));
        }

        return $column;
    }

    public function getName(): string
    {
        return $this->field->getColumn();
    }

    /**
     * Get column type.
     *
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function isPrimary(): bool
    {
        return $this->field->isPrimary() || in_array($this->type, ['primary', 'bigPrimary']);
    }

    public function isNullable(): bool
    {
        if ($this->hasDefault() && $this->getDefault() === null) {
            return true;
        }

        return $this->hasOption(self::OPT_NULLABLE) && !$this->isPrimary();
    }

    public function hasDefault(): bool
    {
        if ($this->isPrimary()) {
            return false;
        }

        return $this->hasOption(self::OPT_DEFAULT);
    }

    /**
     * @return mixed
     * @throws ColumnException
     *
     */
    public function getDefault()
    {
        if (!$this->hasDefault()) {
            throw new ColumnException("No default value on `{$this->field->getColumn()}`");
        }

        return $this->field->getOptions()->get(self::OPT_DEFAULT);
    }

    /**
     * Render column definition.
     *
     * @throws ColumnException
     */
    public function render(AbstractColumn $column): void
    {
        $column->nullable($this->isNullable());

        try {
            // bypassing call to AbstractColumn->type method (or specialized column method)
            if (\method_exists($column, $this->type) && $this->typeOptions !== []) {
                call_user_func_array([$column, $this->type], $this->typeOptions);
            } else {
                call_user_func_array([$column, 'type'], \array_merge([$this->type], $this->typeOptions));
            }
        } catch (\Throwable $e) {
            throw new ColumnException(
                "Invalid column type definition in '{$column->getTable()}'.'{$column->getName()}'",
                (int) $e->getCode(),
                $e,
            );
        }

        if ($this->isNullable()) {
            $column->defaultValue(null);
        }

        if ($this->hasDefault() && $this->getDefault() !== null) {
            $column->defaultValue($this->getDefault());
            return;
        }

        if ($this->hasOption(self::OPT_CAST_DEFAULT)) {
            // cast default value
            $column->defaultValue($this->castDefault($column));
        }

        $column->setAttributes(\iterator_to_array($this->field->getAttributes()));
    }

    /**
     *
     * @return bool|float|int|string
     */
    private function castDefault(AbstractColumn $column)
    {
        if (in_array($column->getAbstractType(), ['timestamp', 'datetime', 'time', 'date'])) {
            return 0;
        }

        if ($column->getAbstractType() === 'enum') {
            // we can use first enum value as default
            return $column->getEnumValues()[0];
        }

        switch ($column->getType()) {
            case AbstractColumn::INT:
                return 0;
            case AbstractColumn::FLOAT:
                return 0.0;
            case AbstractColumn::BOOL:
                return false;
        }

        return '';
    }

    private function hasOption(string $option): bool
    {
        return $this->field->getOptions()->has($option);
    }
}
