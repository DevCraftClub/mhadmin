<?php

declare(strict_types=1);

namespace Cycle\Schema\Generator;

use Cycle\ORM\Relation;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Exception\RegistryException;
use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\Exception\SchemaException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\InversableInterface;
use Cycle\Schema\Registry;
use Cycle\Schema\Relation as Definition;
use Cycle\Schema\Relation\OptionSchema;
use Cycle\Schema\Relation\RelationSchema;
use Cycle\Schema\RelationInterface;

/**
 * Generate relations based on their schematic definitions.
 */
final class GenerateRelations implements GeneratorInterface
{
    // aliases between option names and their internal IDs
    public const OPTION_MAP = [
        'nullable' => Relation::NULLABLE,
        'cascade' => Relation::CASCADE,
        'load' => Relation::LOAD,
        'innerKey' => Relation::INNER_KEY,
        'outerKey' => Relation::OUTER_KEY,
        'morphKey' => Relation::MORPH_KEY,
        'through' => Relation::THROUGH_ENTITY,
        'throughInnerKey' => Relation::THROUGH_INNER_KEY,
        'throughOuterKey' => Relation::THROUGH_OUTER_KEY,
        'throughWhere' => Relation::THROUGH_WHERE,
        'where' => Relation::WHERE,
        'collection' => Relation::COLLECTION_TYPE,
        'orderBy' => Relation::ORDER_BY,
        'fkCreate' => RelationSchema::FK_CREATE,
        'fkAction' => RelationSchema::FK_ACTION,
        'fkOnDelete' => RelationSchema::FK_ON_DELETE,
        'indexCreate' => RelationSchema::INDEX_CREATE,
        'morphKeyLength' => RelationSchema::MORPH_KEY_LENGTH,
        'embeddedPrefix' => RelationSchema::EMBEDDED_PREFIX,

        // deprecated
        'though' => Relation::THROUGH_ENTITY,
        'thoughInnerKey' => Relation::THROUGH_INNER_KEY,
        'thoughOuterKey' => Relation::THROUGH_OUTER_KEY,
        'thoughWhere' => Relation::THROUGH_WHERE,
    ];

    /** @var OptionSchema */
    private $options;

    /** @var RelationInterface[] */
    private $relations = [];

    public function __construct(?array $relations = null, ?OptionSchema $optionSchema = null)
    {
        $relations = $relations ?? self::getDefaultRelations();
        $this->options = $optionSchema ?? new OptionSchema(self::OPTION_MAP);

        foreach ($relations as $id => $relation) {
            if (!$relation instanceof RelationInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid relation type, RelationInterface excepted, `%s` given',
                        is_object($relation) ? get_class($relation) : gettype($relation),
                    ),
                );
            }

            $this->relations[$id] = $relation;
        }
    }

    public function run(Registry $registry): Registry
    {
        foreach ($registry as $entity) {
            $this->register($registry, $entity);
        }

        foreach ($registry as $entity) {
            $this->inverse($registry, $entity);
        }

        return $registry;
    }

    protected static function getDefaultRelations(): array
    {
        return [
            'embedded' => new Definition\Embedded(),
            'belongsTo' => new Definition\BelongsTo(),
            'hasOne' => new Definition\HasOne(),
            'hasMany' => new Definition\HasMany(),
            'refersTo' => new Definition\RefersTo(),
            'manyToMany' => new Definition\ManyToMany(),
            'belongsToMorphed' => new Definition\Morphed\BelongsToMorphed(),
            'morphedHasOne' => new Definition\Morphed\MorphedHasOne(),
            'morphedHasMany' => new Definition\Morphed\MorphedHasMany(),
        ];
    }

    protected function register(Registry $registry, Entity $entity): void
    {
        $role = $entity->getRole();
        \assert($role !== null);

        foreach ($entity->getRelations() as $name => $r) {
            $schema = $this->initRelation($r->getType())->withContext(
                $name,
                $role,
                $r->getTarget(),
                $this->options->withOptions($r->getOptions()),
            );

            // compute relation values (field names, related entities and etc)
            try {
                $schema->compute($registry);
            } catch (RelationException $e) {
                throw new SchemaException(
                    "Unable to compute relation `{$role}`.`{$name}`",
                    $e->getCode(),
                    $e,
                );
            }

            $registry->registerRelation($entity, $name, $schema);
        }
    }

    protected function inverse(Registry $registry, Entity $entity): void
    {
        foreach ($entity->getRelations() as $name => $r) {
            if (!$r->isInversed()) {
                continue;
            }

            $inverseName = $r->getInverseName();
            $inverseType = $r->getInverseType();
            \assert(!empty($inverseName) && !empty($inverseType));

            $schema = $registry->getRelation($entity, $name);
            if (!$schema instanceof InversableInterface) {
                throw new SchemaException('Unable to inverse relation of type ' . get_class($schema));
            }

            foreach ($schema->inverseTargets($registry) as $target) {
                try {
                    $inversed = $schema->inverseRelation(
                        $this->initRelation($inverseType),
                        $inverseName,
                        $r->getInverseLoad(),
                    );

                    $registry->registerRelation($target, $inverseName, $inversed);
                } catch (RelationException $e) {
                    throw new SchemaException(
                        "Unable to inverse relation `{$entity->getRole()}`.`{$name}`",
                        $e->getCode(),
                        $e,
                    );
                }
            }
        }
    }

    protected function initRelation(string $type): RelationInterface
    {
        if (!isset($this->relations[$type])) {
            throw new RegistryException("Undefined relation type `{$type}`");
        }

        return $this->relations[$type];
    }
}
