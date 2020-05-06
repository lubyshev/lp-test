<?php
declare(strict_types=1);

namespace Lubyshev\Models;

use yii\db\Exception;

class Page extends Model
{
    public const TABLE_NAME = 'pages';

    public const KEY_FOLDER_ID = 'folder_id';
    public const KEY_TITLE     = 'title';
    public const KEY_STATE     = 'state';
    public const KEY_TEXT      = 'text';

    public const STATE_EMPTY     = 'empty';
    public const STATE_DRAFT     = 'draft';
    public const STATE_PUBLISHED = 'published';

    public const STATE_LIST = [
        self::STATE_EMPTY,
        self::STATE_DRAFT,
        self::STATE_PUBLISHED,
    ];

    private ?Folder $folder = null;

    public function getTitle(): ?string
    {
        $value = $this->get(self::KEY_TITLE);

        return $value ? (string)$value : null;
    }

    public function setTitle(string $value): self
    {
        $value = trim($value);
        $this->set(self::KEY_TITLE, $value);

        return $this;
    }

    public function getState(): ?string
    {
        $value = $this->get(self::KEY_STATE);

        return $value ? (string)$value : null;
    }

    public function markStateAsEmpty(): self
    {
        $this->set(self::KEY_STATE, self::STATE_EMPTY);

        return $this;
    }

    public function markStateAsDraft(): self
    {
        $this->set(self::KEY_STATE, self::STATE_DRAFT);

        return $this;
    }

    public function markStateAsPublished(): self
    {
        $this->set(self::KEY_STATE, self::STATE_PUBLISHED);

        return $this;
    }

    public function getText(): ?string
    {
        $value = $this->get(self::KEY_TEXT);

        return $value ? (string)$value : null;
    }

    public function setText(?string $value): self
    {
        $this->set(self::KEY_TEXT, $value, true);

        return $this;
    }

    public function getFolderId(): int
    {
        return (int)$this->get(self::KEY_FOLDER_ID);
    }

    public function setFolderId(int $value): self
    {
        $this->set(self::KEY_FOLDER_ID, $value);

        return $this;
    }

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(Folder $value): self
    {
        $this->folder = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

}
