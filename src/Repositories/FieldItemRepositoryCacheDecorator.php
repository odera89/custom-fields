<?php namespace WebEd\Plugins\CustomFields\Repositories;

use WebEd\Base\Caching\Repositories\Eloquent\EloquentBaseRepositoryCacheDecorator;
use WebEd\Plugins\CustomFields\Repositories\Contracts\FieldItemContract;

class FieldItemRepositoryCacheDecorator extends EloquentBaseRepositoryCacheDecorator implements FieldItemContract
{
    public function updateFieldItem($id, array $data)
    {
        return $this->afterUpdate(__FUNCTION__, func_get_args());
    }
}
