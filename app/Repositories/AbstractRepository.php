<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Str;

abstract class AbstractRepository
{
    protected string $model_class;

    public function __construct()
    {
        $model_name = Str::replace('Repository', '', class_basename($this));

        $this->model_class = 'App\\Models\\'.$model_name;
    }

    public function create(array $data): Model
    {
        $model = $this->model_class::create($data);

        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        if (get_class($model) !== $this->model_class) {
            throw new InvalidArgumentException('Invalid model given');
        }

        $model->fill($data);

        if ($model->isDirty()) {
            $model->save();
        }

        return $model;
    }

    public function delete(Model $model): void
    {
        if (get_class($model) !== $this->model_class) {
            throw new InvalidArgumentException('Invalid model given');
        }

        $model->delete();
    }
}
