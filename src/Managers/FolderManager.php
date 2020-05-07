<?php
declare(strict_types=1);

namespace Lubyshev\Managers;

use Lubyshev\Models\Folder;

class FolderManager
{
    public static function toArray(Folder $folder): array
    {
        return [
            'id'    => $folder->getPk()['id'],
            'title' => $folder->getTitle(),
        ];
    }

}