<?php

declare(strict_types=1);

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public static function all():Collection;
    public static function create(array $data):Model|null;
    public static function find(int $id):Model|null;
    public static function delete(int $id):bool;
    public static function update(int $id, array $data):int;
    public static function loadModel():Model;
}
