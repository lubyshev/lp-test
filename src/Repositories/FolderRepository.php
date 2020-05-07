<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use Lubyshev\Models\Folder;

class FolderRepository extends Repository
{
    /**
     * Поиск по первичному ключу.
     *
     * @param array $pk Первичный ключ
     *
     * @return \Lubyshev\Models\Folder|null
     */
    public static function findByPk(array $pk): ?Folder
    {
        $model = null;
        $data  = self::getDataByPk(Folder::class, $pk);
        if ($data) {
            $model = self::fillModelFromArray($data);
        }

        return $model;
    }

    /**
     * Заполняет модель данными из массива.
     *
     * @param array $data Данные
     *
     * @return \Lubyshev\Models\Folder
     */
    protected static function fillModelFromArray(array $data): Folder
    {
        return (new Folder())
            ->setPk(['id' => $data['id']])
            ->markRecordAsExists()
            ->setTitle($data[Folder::KEY_TITLE])
            ->unsetChanges();
    }

    /**
     * @param int  $limit
     * @param int  $page
     * @param bool $orderDesc
     * @param int  $fromId
     *
     * @return Folder[]|null
     * @throws \Exception
     */
    public static function getTitleOrderedList(
        int $limit,
        int $offset = 0,
        bool $orderDesc = false,
        int $fromId = 1
    ): ?array {
        $t     = Folder::tableName();
        $pk    = Folder::getPrimaryKey();
        $where = [];
        foreach ($pk as $key) {
            $where[] = ['key' => $key, 'sign' => '>='];
        }

        return self::getList(
            Folder::class,
            $limit,
            $offset,
            '`title`'.($orderDesc ? ' DESC' : ''),
            self::whereAndFromArray($t, $where),
            ['id' => $fromId]
        );
    }

}
