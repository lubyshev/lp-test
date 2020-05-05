<?php
declare(strict_types=1);

namespace Lubyshev\Repositories;

use Lubyshev\Models\ModelInterface;

interface RepositoryInterface
{
    public static function getDataByPk(string $modelClass, array $pk): ?array;

    public static function findByPk(array $pk): ?ModelInterface;
}
