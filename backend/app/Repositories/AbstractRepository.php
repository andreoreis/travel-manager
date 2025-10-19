<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements RepositoryInterface
{
    protected static $model;

    public static function all():Collection{
        return self::loadModel()::all();
    }

    public static function find(int $id):Model|null{
        return self::loadModel()::query()->find($id);
    }

    public static function create(array $data = []):Model|null{
        return self::loadModel()::query()->create($data);
    }

    public static function delete(int $id):bool{
        return self::loadModel()::query()->where(['id' => $id])->delete();
    }

    public static function update(int $id, array $data = []):Model{
        self::loadModel()::query()->where(['id' => $id])->update($data);
        return self::find($id);
    }

    public static function loadModel():Model{
        return app(static::$model);
    }
}
