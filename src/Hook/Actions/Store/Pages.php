<?php namespace WebEd\Plugins\CustomFields\Hook\Actions\Store;

use WebEd\Base\Core\Http\Controllers\BaseAdminController;

class Pages extends AbstractStore
{
    /**
     * @var string
     */
    protected $repositoryInterface = 'WebEd\Base\Pages\Repositories\Contracts\PageContract';

    /**
     * @param $id
     * @param array $result
     * @param BaseAdminController $controller
     */
    public function afterCreate(array $result, BaseAdminController $controller)
    {
        /**
         * Get object from result
         */
        $object = array_get($result, 'data', null);

        if (array_get($result, 'error', true) || !$object) {
            return;
        }

        $this->afterSaveContent($object->id, $result, $controller);
    }
}
