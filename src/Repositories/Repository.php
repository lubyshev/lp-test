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
     * @param int    $offset     Смещение
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
        $table = $modelClass::tableName();
        $sql   =
            " SELECT * FROM `{$table}`".
            " WHERE ".$where.
            " ORDER BY {$orderBy}".
            " LIMIT {$limit}".
            " OFFSET {$offset}";
        $query = Yii::$app->db->createCommand($sql);
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
        if ($offset < 0) {
            $offset = 0;
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

    public static function save(ModelInterface $model): bool
    {
        if ($model->isNewRecord()) {
            $result = self::insert($model);
        } else {
            $result = self::update($model);
        }

        return $result;
    }

    protected static function insert(ModelInterface $model): bool
    {
        $table  = $model::tableName();
        $fields = $model->getChangedFields();
        $insert = [];
        foreach ($fields as $field) {
            $insert[":{$field}"] = $model->get($field);
        }
        $sql =
            " INSERT INTO `{$table}` (`".implode('`, `', $fields)."`)".
            " VALUES (".implode(', ', array_keys($insert)).")";
        $cmd = Yii::$app->db->createCommand($sql);
        foreach ($insert as $key => $value) {
            $cmd->bindValue($key, $value);
        }

        return (bool)$cmd->execute();
    }

    protected static function update(ModelInterface $model): bool
    {
        $table  = $model::tableName();
        $pk     = $model::getPrimaryKey();
        $fields = $model->getChangedFields();
        $where  = [];
        foreach ($pk as $key) {
            $where[] = ['key' => $key, 'sign' => '='];
        }
        $update = [];
        foreach ($fields as $field) {
            $update[] = "`{$field}` = :{$field}";
        }
        $sql    =
            " UPDATE `{$table}` SET ".implode(', ', $update).
            " WHERE ".self::whereAndFromArray($table, $where);
        $cmd    = Yii::$app->db->createCommand($sql);
        $fields = array_merge($pk, $fields);
        foreach ($fields as $field) {
            $cmd->bindValue(':'.$field, $model->get($field));
        }

        return (bool)$cmd->execute();
    }

    public static function delete(ModelInterface $model): bool
    {
        if ($model->isNewRecord()) {
            throw new \InvalidArgumentException("Can`t delete new record.");
        }
        $table = $model::tableName();
        $pk    = $model::getPrimaryKey();
        $where = [];
        foreach ($pk as $key) {
            $where[] = ['key' => $key, 'sign' => '='];
        }
        $sql =
            " DELETE FROM `{$table}`".
            " WHERE ".self::whereAndFromArray($table, $where);
        $cmd = Yii::$app->db->createCommand($sql);
        foreach ($pk as $field) {
            $cmd->bindValue(':'.$field, $model->get($field));
        }

        return (bool)$cmd->execute();
    }

}
