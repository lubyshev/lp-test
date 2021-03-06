<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use Lubyshev\Models\ModelInterface;

interface RepositoryInterface
{
    /**
     * Поиск по первичному ключу.
     *
     * @param array $pk Первичный ключ
     *
     * @return \Lubyshev\Models\ModelInterface|null
     */
    public static function findByPk(array $pk): ?ModelInterface;

    /**
     * Возвращает список моделей.
     *
     * @param string $modelClass ,
     * @param int    $limit
     * @param int    $page
     * @param string $orderBy
     * @param string $where
     * @param array  $bindParams Key-pair параметря для PDO::bindValue()
     *
     * @return array
     */
    public static function getList(
        string $modelClass,
        int $limit,
        int $page,
        string $orderBy,
        string $where,
        array $bindParams
    ): ?array;

    public static function save(ModelInterface $model): bool;

    public static function delete(ModelInterface $model): bool;

}
