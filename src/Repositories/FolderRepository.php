<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use Lubyshev\Models\Folder;

class FolderRepository extends Repository
{
    public static function findByPk(array $pk): ?Folder
    {
        $model = new Folder();
        $data  = self::getDataByPk(Folder::class, $pk);
        if ($data) {
            $model
                ->setPk(['id' => $data['id']])
                ->markRecordAsExists()
                ->setTitle($data[$model::KEY_TITLE])
                ->unsetChanges();
        } else {
            $model = null;
        }

        return $model;
    }
}
