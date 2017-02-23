<?php namespace WebEd\Plugins\CustomFields\Providers;

use Illuminate\Support\ServiceProvider;
use WebEd\Plugins\CustomFields\Models\CustomField;
use WebEd\Plugins\CustomFields\Models\FieldGroup;
use WebEd\Plugins\CustomFields\Models\FieldItem;
use WebEd\Plugins\CustomFields\Repositories\Contracts\CustomFieldContract;
use WebEd\Plugins\CustomFields\Repositories\Contracts\FieldGroupContract;
use WebEd\Plugins\CustomFields\Repositories\Contracts\FieldItemContract;
use WebEd\Plugins\CustomFields\Repositories\CustomFieldRepository;
use WebEd\Plugins\CustomFields\Repositories\CustomFieldRepositoryCacheDecorator;
use WebEd\Plugins\CustomFields\Repositories\FieldGroupRepository;
use WebEd\Plugins\CustomFields\Repositories\FieldGroupRepositoryCacheDecorator;
use WebEd\Plugins\CustomFields\Repositories\FieldItemRepository;
use WebEd\Plugins\CustomFields\Repositories\FieldItemRepositoryCacheDecorator;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FieldGroupContract::class, function () {
            $repository = new FieldGroupRepository(new FieldGroup);

            return $repository;
        });
        $this->app->bind(FieldItemContract::class, function () {
            $repository = new FieldItemRepository(new FieldItem);

            return $repository;
        });
        $this->app->bind(CustomFieldContract::class, function () {
            $repository = new CustomFieldRepository(new CustomField);

            if (config('webed-caching.repository.enabled')) {
                return new CustomFieldRepositoryCacheDecorator($repository);
            }

            return $repository;
        });
    }
}
