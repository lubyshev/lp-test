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
     * @param array  $fromPk     С какого PK начинать
     *
     * @return array|null
     */
    protected static function getDataList(
        string $modelClass,
        int $limit,
        int $page,
        string $orderBy,
        array $fromPk
    ): ?array {
        /** @var \Lubyshev\Models\ModelInterface $modelClass */
        $table    = $modelClass::tableName();
        $pkFields = $modelClass::getPrimaryKey();
        $where    = [];
        foreach ($pkFields as $key) {
            $where[] = "`{$table}`.`{$key}` >= :{$key}";
        }
        $offset = $limit * ($page - 1);
        $query  = Yii::$app->db->createCommand(
            "SELECT * FROM `{$table}`".
            " WHERE (".implode(' AND ', $where).")".
            " ORDER BY {$orderBy}".
            " LIMIT {$limit}".
            " OFFSET {$offset}"
        );
        foreach ($pkFields as $key) {
            $query->bindValue(':'.$key, $fromPk[$key]);
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
        int $page,
        string $orderBy,
        array $fromPk
    ): ?array {
        if ($limit <= 0) {
            throw new Exception("Invalid limit: {$limit}.");
        }
        if ($page < 1) {
            $page = 1;
        }
        if ($fromPk < 1) {
            $fromPk = 1;
        }
        $items    = null;
        $dataList = self::getDataList(
            $modelClass,
            $limit,
            $page,
            $orderBy,
            $fromPk
        );
        if ($dataList) {
            foreach ($dataList as $data) {
                $items[] = static::fillModelFromArray($data);
            }
        }

        return empty($items) ? null : $items;
    }


}