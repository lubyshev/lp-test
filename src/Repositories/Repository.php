<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use Yii;

abstract class Repository implements RepositoryInterface
{
    public static function getDataByPk(string $modelClass, array $pk): ?array
    {
        $table    = $modelClass::tableName();
        $pkFields = $modelClass::getPrimaryKeyName();
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
}