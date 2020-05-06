<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use yii;
use \Exception;
use Lubyshev\Models\Page;

class PageRepository extends Repository
{
    /**
     * @todo Сделать настройку в конфиге.
     */
    private const DEFAULT_PATH = '/runtime/pages';

    /**
     * Поиск по первичному ключу.
     *
     * @param array       $pk         Первичный ключ
     * @param bool        $withFolder Загрузить Folder
     * @param string|null $path       Путь размещения файла с текстом
     *
     * @return \Lubyshev\Models\Page|null
     * @throws \yii\db\Exception
     */
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
            if (!in_array($data['state'], Page::STATES_LIST)) {
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
            if ($folder) {
                $model->setFolder($folder);
            }
            $model->unsetChanges();
            self::fillModelWithText($model, $path);
        }

        return $model;
    }

    /**
     * Заполняет модель данными из массива.
     *
     * @param array $data Данные
     *
     * @return \Lubyshev\Models\Page
     */
    protected static function fillModelFromArray(array $data): Page
    {
        $model = new Page();
        $model
            ->setPk(['id' => $data['id']])
            ->markRecordAsExists()
            ->setTitle($data[Page::KEY_TITLE])
            ->setFolderId($data[Page::KEY_FOLDER_ID]);
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
        if ($hasText) {
            $model->markContentAsExists();
        } else {
            $model->markContentAsEmpty();
        }

        return $model;
    }

    private static function fillModelWithText(Page $model, string $path): void
    {
        if ($model->contentExists()) {
            $model->setText(self::getText($model, $path));
            // Если содержимое пропало.
            if (empty($model->getText())) {
                $model->markStateAsEmpty();
            } else {
                // Если файл добавили вручную.
                if (Page::STATE_EMPTY === $model->getState()) {
                    $model->markStateAsDraft();
                }
            }
        }
    }

    /**
     * Возвращает текст страницы.
     *
     * @param \Lubyshev\Models\Page $model Модель.
     * @param string                $path  Путь размещения файла с текстом
     *
     * @return string|null
     * @throws \Exception
     */
    private static function getText(Page $model, string $path): ?string
    {
        $id         = $model->getPk()['id'];
        $fileName   = $path."/{$id}.html";
        $fileExists = file_exists($fileName);
        if (!$fileExists) {
            $result = null;
        } else {
            $result = file_get_contents($fileName);
            if (empty(trim($result))) {
                $result = false;
            }
        }
        // Если файл пустой.
        if (!$result && $fileExists) {
            unlink($fileName);
        }

        return $result ? $result : null;
    }
}