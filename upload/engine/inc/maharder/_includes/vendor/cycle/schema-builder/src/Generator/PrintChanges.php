<?php

declare(strict_types=1);

namespace Cycle\Schema\Generator;

use Cycle\Database\Schema\ComparatorInterface;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Cycle\Database\Schema\AbstractTable;
use Symfony\Component\Console\Output\OutputInterface;

final class PrintChanges implements GeneratorInterface
{
    private array $changes = [];

    public function __construct(
        private OutputInterface $output,
    ) {}

    public function run(Registry $registry): Registry
    {
        $this->changes = [];
        foreach ($registry->getIterator() as $e) {
            if ($registry->hasTable($e)) {
                $table = $registry->getTableSchema($e);

                if ($this->hasTableChanges($table)) {
                    $key = $registry->getDatabase($e) . ':' . $registry->getTable($e);
                    $this->changes[$key] = [
                        'database' => $registry->getDatabase($e),
                        'table' => $registry->getTable($e),
                        'schema' => $table,
                    ];
                }
            }
        }

        if (!$this->hasChanges()) {
            $this->output->writeln('<fg=yellow>No database changes has been detected</fg=yellow>');

            return $registry;
        }

        $this->output->writeln('<info>Schema changes:</info>');

        foreach ($this->changes as $change) {
            $this->output->write(\sprintf('• <fg=cyan>%s.%s</fg=cyan>', $change['database'], $change['table']));
            $this->describeChanges($change['schema']);
        }

        return $registry;
    }

    public function hasChanges(): bool
    {
        return $this->changes !== [];
    }

    private function describeChanges(AbstractTable $table): void
    {
        if ($table->getStatus() === AbstractTable::STATUS_DECLARED_DROPPED) {
            $this->output->writeln('    - drop table');
            return;
        }

        if (!$this->output->isVerbose()) {
            $this->output->writeln(
                \sprintf(
                    ': <fg=green>%s</fg=green> change(s) detected',
                    $this->numChanges($table),
                ),
            );

            return;
        }

        $this->output->write("\n");

        if (!$table->exists()) {
            $this->output->writeln('    - create table');
        }

        $cmp = $table->getComparator();

        $this->describeColumns($cmp);
        $this->describeIndexes($cmp);
        $this->describeFKs($cmp);
    }

    private function describeColumns(ComparatorInterface $cmp): void
    {
        foreach ($cmp->addedColumns() as $column) {
            $this->output->writeln("    - add column <fg=yellow>[{$column->getName()}]</fg=yellow>");
        }

        foreach ($cmp->droppedColumns() as $column) {
            $this->output->writeln("    - drop column <fg=yellow>[{$column->getName()}]</fg=yellow>");
        }

        foreach ($cmp->alteredColumns() as $column) {
            $column = $column[0];
            $this->output->writeln("    - alter column <fg=yellow>[{$column->getName()}]</fg=yellow>");
        }
    }

    private function describeIndexes(ComparatorInterface $cmp): void
    {
        foreach ($cmp->addedIndexes() as $index) {
            $index = \implode(', ', $index->getColumns());
            $this->output->writeln("    - add index on <fg=yellow>[{$index}]</fg=yellow>");
        }

        foreach ($cmp->droppedIndexes() as $index) {
            $index = \implode(', ', $index->getColumns());
            $this->output->writeln("    - drop index on <fg=yellow>[{$index}]</fg=yellow>");
        }

        foreach ($cmp->alteredIndexes() as $index) {
            $index = $index[0];
            $index = \implode(', ', $index->getColumns());
            $this->output->writeln("    - alter index on <fg=yellow>[{$index}]</fg=yellow>");
        }
    }

    private function describeFKs(ComparatorInterface $cmp): void
    {
        foreach ($cmp->addedForeignKeys() as $fk) {
            $fkColumns = \implode(', ', $fk->getColumns());
            $this->output->writeln("    - add foreign key on <fg=yellow>[{$fkColumns}]</fg=yellow>");
        }

        foreach ($cmp->droppedForeignKeys() as $fk) {
            $fkColumns = \implode(', ', $fk->getColumns());
            $this->output->writeln("    - drop foreign key on <fg=yellow>[{$fkColumns}]</fg=yellow>");
        }

        foreach ($cmp->alteredForeignKeys() as $fk) {
            $fk = $fk[0];
            $fkColumns = \implode(', ', $fk->getColumns());
            $this->output->writeln("    - alter foreign key on <fg=yellow>[{$fkColumns}]</fg=yellow>");
        }
    }

    private function numChanges(AbstractTable $table): int
    {
        $cmp = $table->getComparator();

        return \count($cmp->addedColumns())
            + \count($cmp->droppedColumns())
            + \count($cmp->alteredColumns())
            + \count($cmp->addedIndexes())
            + \count($cmp->droppedIndexes())
            + \count($cmp->alteredIndexes())
            + \count($cmp->addedForeignKeys())
            + \count($cmp->droppedForeignKeys())
            + \count($cmp->alteredForeignKeys());
    }

    private function hasTableChanges(AbstractTable $table): bool
    {
        return $table->getComparator()->hasChanges()
            || !$table->exists()
            || $table->getStatus() === AbstractTable::STATUS_DECLARED_DROPPED;
    }
}
