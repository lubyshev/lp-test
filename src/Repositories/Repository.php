<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use Yii;
use \Exception;
use Lubyshev\Models\ModelInterface;

abstract class Repository implements RepositoryInterface
{
    abstract protected static function fillModelFromArray(array $data): ModelInterface;

    /**
     * Возвращает массив с данными модели.
     *
     * @param string $modelClass Класс модели.
     * @param array  $pk         Первичный ключ.
     *
     * @return array|null
     * @throws \yii\db\Exception
     */
    protected static function getDataByPk(string $modelClass, array $pk): ?array
    {
        /** @var \Lubyshev\Models\ModelInterface $modelClass */
        $table    = $modelClass::tableName();
        $pkFields = $modelClass::getPrimaryKey();
        $where    = [];
        foreach ($pkFields as $key) {
            $where[] = "`{$table}`.`{$key}` = {$pk[$key]}";
        }
        $query = Yii::$app->db->createCommand(
            "SELECT * FROM `{$table}` WHERE (".implode(' AND ', $where).")"
        );
        $row   = $query->queryOne();

        return $row ? $row : null;
    }

    /**
     * Возвращает список массивов с данными моделей.
     *
     * @param string $modelClass Класс модели.
     * @param int    $limit      Склоько записей
     * @param int    $page       Номер страницы
     * @param string $orderBy    Обратная сортировка
     * @param string $where      Условие WHERE
     * @param array  $bindParams Key-pair параметря для PDO::bindValue()
     *
     * @return array|null
     */
    protected static function getDataList(
        string $modelClass,
        int $limit,
        int $offset,
        string $orderBy,
        string $where,
        array $bindParams
    ): ?array {
        /** @var \Lubyshev\Models\ModelInterface $modelClass */
        $table  = $modelClass::tableName();
        $sql    =
            " SELECT * FROM `{$table}`".
            " WHERE ".$where.
            " ORDER BY {$orderBy}".
            " LIMIT {$limit}".
            " OFFSET {$offset}";
        $query  = Yii::$app->db->createCommand($sql);
        foreach ($bindParams as $key => $value) {
            $query->bindValue(':'.$key, $bindParams[$key]);
        }
        $rows = $query->queryAll();

        return $rows ? $rows : null;
    }

    /**
     * @inheritDoc
     */
    public static function getList(
        string $modelClass,
        int $limit,
        int $offset,
        string $orderBy,
        string $where,
        array $bindParams
    ): ?array {
        if ($limit <= 0) {
            throw new Exception("Invalid limit: {$limit}.");
        }
        if ($offset < 1) {
            $offset = 1;
        }
        $items    = null;
        $dataList = self::getDataList(
            $modelClass,
            $limit,
            $offset,
            $orderBy,
            $where,
            $bindParams
        );
        if ($dataList) {
            foreach ($dataList as $data) {
                $items[] = static::fillModelFromArray($data);
            }
        }

        return empty($items) ? null : $items;
    }

    public static function whereAndFromArray($table, $items)
    {
        return implode(' AND ', self::createWhereArray($table, $items));
    }

    public static function whereOrFromArray($table, $items): string
    {
        return implode(' OR ', self::createWhereArray($table, $items));
    }

    private static function createWhereArray($table, $items): array
    {
        $where = [];
        foreach ($items as $item) {
            $where[] = "`{$table}`.`{$item['key']}` {$item['sign']} :{$item['key']}";
        }

        return $where;
    }


}
