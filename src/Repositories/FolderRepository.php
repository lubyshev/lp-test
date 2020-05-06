<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use Lubyshev\Models\Folder;

class FolderRepository extends Repository
{
    public static function findByPk(array $pk): ?Folder
    {
        $model = null;
        $data  = self::getDataByPk(Folder::class, $pk);
        if ($data) {
            $model = self::fillModelFromArray($data);
        }

        return $model;
    }

    private static function fillModelFromArray(array $data): Folder
    {
        $model = (new Folder())
            ->setPk(['id' => $data['id']])
            ->markRecordAsExists()
            ->setTitle($data[Folder::KEY_TITLE])
            ->unsetChanges();

        return $model;
    }

}
