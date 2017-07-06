<?php

namespace NieFufeng\LaravelRbac\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = config('rbac.connection', config('database.default'));
    }
}