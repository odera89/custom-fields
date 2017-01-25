<?php namespace WebEd\Plugins\CustomFields\Hook\Actions;

use Illuminate\Support\Facades\Route;

class AssetsInjection
{

    /**
     * @var string
     */
    protected $currentRouteName;

    protected $allowedRoute = [
        'admin::pages.create.get',
        'admin::pages.edit.get',

        /**
         * Blog
         */
        'admin::blog.posts.create.get',
        'admin::blog.posts.edit.get',
        'admin::blog.categories.create.get',
        'admin::blog.categories.edit.get',
    ];

    public function __construct()
    {
        $this->currentRouteName = Route::currentRouteName();
    }

    /**
     * @return bool
     */
    public function checkAllowedRoute()
    {
        if (in_array($this->currentRouteName, $this->allowedRoute)) {
            return true;
        }
        return false;
    }

    /**
     * Render js
     */
    public function renderJs()
    {
        if (!$this->checkAllowedRoute()) {
            return;
        }

        echo view('webed-custom-fields::admin._script-templates.render-custom-fields')->render();
    }
}
