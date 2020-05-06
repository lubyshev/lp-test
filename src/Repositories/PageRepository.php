<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use yii;
use yii\db\Exception;
use Lubyshev\Models\Folder;
use Lubyshev\Models\Page;

class PageRepository extends Repository
{
    /**
     * @todo Сделать настройку в конфиге.
     */
    private const DEFAULT_PATH = '/runtime/pages';

    public static function findByPk(
        array $pk,
        bool $withFolder = false,
        ?string $path = null
    ): ?Page {
        if (empty($path)) {
            $path = Yii::$app->basePath.self::DEFAULT_PATH;
        }
        if (!is_dir($path)) {
            throw new \Exception("Directory '{$path}': folder does not exists.");
        }
        if (!is_writable($path)) {
            throw new \Exception("Directory '{$path}': permission denied.");
        }
        $model = null;
        $data  = self::getDataByPk(Page::class, $pk);
        if ($data) {
            if (!in_array($data['state'], Page::STATE_LIST)) {
                throw new Exception(
                    "Invalid page state(id: {$data['id']}, state: {$data['state']})."
                );
            }
            $folder = null;
            if ($withFolder) {
                $folder = FolderRepository::findByPk(['id' => $data['folder_id']]);
                if (!$folder) {
                    throw new Exception(
                        "Invalid folder(id: {$data['folder_id']}) for page(id: {$data['id']})."
                    );
                }
            }
            $model = self::fillModelFromArray($data, $folder, $path);
        }

        return $model;
    }

    private static function fillModelFromArray(array $data, ?Folder $folder, string $path): Page
    {
        $model = new Page();
        $model
            ->setPk(['id' => $data['id']])
            ->markRecordAsExists()
            ->setTitle($data[$model::KEY_TITLE])
            ->setFolderId($data[$model::KEY_FOLDER_ID]);
        if ($folder) {
            $model->setFolder($folder);
        }
        $hasText = true;
        switch ($data['state']) {
            case Page::STATE_EMPTY:
                $model->markStateAsEmpty();
                $hasText = false;
                break;
            case Page::STATE_DRAFT:
                $model->markStateAsDraft();
                break;
            case Page::STATE_PUBLISHED:
                $model->markStateAsPublished();
                break;
        }
        $model->unsetChanges();
        if ($hasText) {
            $model->setText(self::getText($model, $path));
            if (empty($model->getText())) {
                $model->markStateAsEmpty();
            } else {
                if (Page::STATE_EMPTY === $model->getState()) {
                    $model->markStateAsDraft();
                }
            }
        }

        return $model;
    }

    private static function getText(Page $model, string $path): ?string
    {
        $id       = $model->getPk()['id'];
        $fileName = $path."/{$id}.html";
        if (!file_exists($fileName)) {
            throw new Exception(
                "Page file(id: {$id}, path: {$fileName}) does not exists."
            );
        }
        $result = file_get_contents($fileName);
        if (empty(trim($result))) {
            $result = false;
        }
        if (!$result) {
            unlink($fileName);
        }

        return $result ? $result : null;
    }
}