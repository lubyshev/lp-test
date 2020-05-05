<?php
declare(strict_types=1);

namespace Lubyshev\Models;

abstract class Model implements ModelInterface
{
    private const DEFAULT_PK = ['id'];

    private bool $isNew = true;

    private array $items;

    private array $changedItems;

    /**
     * @inheritDoc
     */
    public static function getPrimaryKeyName(): array
    {
        return self::DEFAULT_PK;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name)
    {
        return $this->items[$name];
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, $value, bool $ignoreChanges = false): self
    {
        $this->items[$name] = $value;
        if (!$ignoreChanges) {
            $this->changedItems[$name] = 1;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChangedFields(): array
    {
        return array_keys($this->changedItems);
    }

    /**
     * @inheritDoc
     */
    public function isNewRecord(): bool
    {
        return $this->isNew;
    }

    /**
     * @inheritDoc
     */
    public function markRecordAsNew(): ModelInterface
    {
        $this->isNew = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function markRecordAsExists(): ModelInterface
    {
        $this->isNew = false;

        return $this;
    }
}
