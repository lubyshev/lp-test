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
        return array_key_exists($name, $this->items) ? $this->items[$name] : null;
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
    public function markRecordAsNew(): self
    {
        $this->isNew = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function markRecordAsExists(): self
    {
        $this->isNew = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPk(): ?array
    {
        $result = [];
        $pk     = static::getPrimaryKeyName();
        foreach ($pk as $key) {
            $value = $this->get($key);
            if ($value) {
                $result[$key] = $value;
            }
        }
        if (empty($result)) {
            $result = null;
        } elseif (count($result) !== count($pk)) {
            throw new \Exception("Incomplete primary key.");
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setPk(array $value): self
    {
        if ($this->checkPk($value)) {
            foreach (static::getPrimaryKeyName() as $key) {
                $this->set($key, $value[$key]);
            }
        }

        return $this;
    }

    protected function checkPk(array $value): bool
    {
        $pk   = static::getPrimaryKeyName();
        $diff = array_merge(
            array_diff($pk, array_keys($value)),
            array_diff(array_keys($value), $pk),
        );
        if (!empty($diff)) {
            throw new \InvalidArgumentException("Invalid primary key field name.");
        }
        foreach ($pk as $key) {
            if (empty($value[$key])) {
                throw new \InvalidArgumentException("Invalid primary key value.");
                break;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    abstract public static function tableName(): string;

    /**
     * @inheritDoc
     */
    public function unsetChanges(?string $name = null): self
    {
        if (isset($name)) {
            if (isset($this->changedItems[$name])) {
                unset($this->changedItems[$name]);
            }
        } else {
            $this->changedItems = [];
        }

        return $this;
    }
}
