<?php namespace WebEd\Plugins\CustomFields\Http\Requests;

use WebEd\Base\Core\Http\Requests\Request;

class UpdateFieldGroupRequest extends Request
{
    public $rules = [

    ];

    public function authorize()
    {
        //return $this->user()->hasPermission('edit-page');
        return true;
    }
}
