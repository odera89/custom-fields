<?php namespace WebEd\Plugins\CustomFields\Repositories;

use WebEd\Base\Caching\Services\Traits\Cacheable;
use WebEd\Base\Core\Repositories\Eloquent\EloquentBaseRepository;
use WebEd\Base\Caching\Services\Contracts\CacheableContract;
use WebEd\Plugins\CustomFields\Repositories\Contracts\CustomFieldContract;

class CustomFieldRepository extends EloquentBaseRepository implements CustomFieldContract, CacheableContract
{
    use Cacheable;

    protected $rules = [
        'use_for' => 'required',
        'use_for_id' => 'required|integer',
        'parent_id' => 'integer',
        'type' => 'required|string|max:255',
        'slug' => 'required|between:3,255|alpha_dash',
        'value' => 'nullable|string',
    ];

    protected $editableFields = [
        'use_for',
        'use_for_id',
        'parent_id',
        'type',
        'slug',
        'value',
    ];
}
