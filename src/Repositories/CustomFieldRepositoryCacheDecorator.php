<?php namespace WebEd\Plugins\CustomFields\Repositories;

use WebEd\Base\Caching\Repositories\Eloquent\EloquentBaseRepositoryCacheDecorator;
use WebEd\Plugins\CustomFields\Repositories\Contracts\CustomFieldContract;

class CustomFieldRepositoryCacheDecorator extends EloquentBaseRepositoryCacheDecorator implements CustomFieldContract
{

}
