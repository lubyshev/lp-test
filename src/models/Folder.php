<?php
declare(strict_types=1);

namespace Lubyshev\Models;

class Folder extends Model
{
    public const KEY_TITLE = 'title';

    public function getTitle(): string
    {
        return (string)$this->get(self::KEY_TITLE);
    }

    public function setTitle(string $value): self
    {
        $this->set(self::KEY_TITLE, $value);

        return $this;
    }

}
