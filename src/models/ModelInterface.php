<?php
declare(strict_types=1);

namespace Lubyshev\Models;

interface ModelInterface
{
    /**
     * Является ли запись новой.
     *
     * @return bool
     */
    public function isNewRecord(): bool;

    /**
     * Отмечает запись как новую.
     *
     * @return $this
     */
    public function markRecordAsNew(): self;

    /**
     * Отмечает запись как не новую.
     *
     * @return $this
     */
    public function markRecordAsExists(): self;

    /**
     * Возвращает имена полей первичного ключа таблицы
     *
     * @return array
     */
    public static function getPrimaryKeyName(): array;

    /**
     * Возвращает значение поля.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name);

    /**
     * Устанавливает значение поля.
     *
     * @param string $name
     * @param mixed  $value
     * @param bool   $ignoreChanges
     *
     * @return $this
     */
    public function set(string $name, $value, bool $ignoreChanges = false): self;

    /**
     * Возвращает список измененных полей.
     *
     * @return array
     */
    public function getChangedFields(): array;

}