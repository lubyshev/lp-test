<?php
declare(strict_types=1);

namespace Lubyshev\Models;

interface ModelInterface
{
    /**
     * Возвращает имя таблицы.
     *
     * @return string
     */
    public static function tableName(): string;

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
     * Возвращает PK записи.
     *
     * @return array|null
     */
    public function getPk(): ?array;

    /**
     * Устанавливает PK записи.
     *
     * @param array $value
     *
     * @return $this
     */
    public function setPk(array $value): self;

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
     * Удаляет поле из списка измененных.
     *
     * @param string $name
     *
     * @return $this
     */
    public function unsetChanges(?string $name = null): self;

    /**
     * Возвращает список измененных полей.
     *
     * @return array
     */
    public function getChangedFields(): array;

}