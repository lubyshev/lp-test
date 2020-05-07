<?php
declare(strict_types=1);

namespace Lubyshev\Managers;

use Lubyshev\Models\Page;

class PageManager
{
    public static function toArray(Page $page): array
    {
        $folder = $page->getFolder();

        return [
            'id'        => $page->getPk()['id'],
            'folder_id' => $page->getFolderId(),
            'folder'    => $folder
                ? FolderManager::toArray($folder)
                : null,
            'title'     => $page->getTitle(),
            'state'     => $page->getState(),
            'text'      => $page->getText(),
        ];
    }

}