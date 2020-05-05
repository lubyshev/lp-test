<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use yii;
use yii\db\Exception;
use Lubyshev\Models\Page;

class PageRepository extends Repository
{
    public static function findByPk(array $pk): ?Page
    {
        $model = null;
        $data  = self::getDataByPk(Page::class, $pk);
        if ($data) {
            $folder = FolderRepository::findByPk(['id' => $data['folder_id']]);
            if (!$folder) {
                throw new Exception(
                    "Invalid folder(id: {$data['folder_id']}) for page(id: {$data['id']})."
                );
            }
            if (!in_array(
                $data['state'],
                Page::STATE_LIST
            )) {
                throw new Exception(
                    "Invalid page state(id: {$data['id']}, state: {$data['state']})."
                );
            }
            $path  = Yii::$app->basePath.'/runtime/pages';
            $model = new Page($folder, $path);
            $model->setPk(['id' => $data['id']])
                ->markRecordAsExists()
                ->setFolderId((int)$data['folder_id'])
                ->setTitle($data[$model::KEY_TITLE]);
            $needFileExists = true;
            switch ($data['state']) {
                case Page::STATE_EMPTY:
                    $model->markStateAsEmpty();
                    $needFileExists = false;
                    break;
                case Page::STATE_DRAFT:
                    $model->markStateAsDraft();
                    break;
                case Page::STATE_PUBLISHED:
                    $model->markStateAsPublished();
                    break;
            }
            $fileName   = $path."/{$data['id']}.html";
            $fileExists = file_exists($fileName);
            if ($needFileExists) {
                if (!$fileExists) {
                    throw new Exception(
                        "Page file(id: {$data['id']}, path: {$fileName}) does not exists."
                    );
                }
                $model->setText(file_get_contents($fileName));
            } elseif ($fileExists) {
                unlink($fileName);
            }
            $model->unsetChanges();
        } else {
            $model = null;
        }

        return $model;
    }
}