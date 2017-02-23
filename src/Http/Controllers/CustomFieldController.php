<?php namespace WebEd\Plugins\CustomFields\Http\Controllers;

use WebEd\Base\Core\Http\Controllers\BaseAdminController;

use WebEd\Plugins\CustomFields\Http\DataTables\FieldGroupsListDataTable;
use WebEd\Plugins\CustomFields\Http\Requests\CreateFieldGroupRequest;
use WebEd\Plugins\CustomFields\Http\Requests\UpdateFieldGroupRequest;
use WebEd\Plugins\CustomFields\Repositories\Contracts\FieldGroupContract;
use WebEd\Plugins\CustomFields\Repositories\Contracts\FieldItemContract;
use Yajra\Datatables\Engines\BaseEngine;

class CustomFieldController extends BaseAdminController
{
    protected $module = 'webed-custom-fields';

    /**
     * @var \WebEd\Plugins\CustomFields\Repositories\FieldGroupRepository
     */
    protected $repository;

    /**
     * @var \WebEd\Plugins\CustomFields\Repositories\FieldItemRepository
     */
    protected $itemRepository;

    public function __construct(FieldGroupContract $fieldGroup, FieldItemContract $fieldItem)
    {
        parent::__construct();

        $this->getDashboardMenu($this->module);

        $this->breadcrumbs->addLink('Custom fields', route('admin::custom-fields.index.get'));

        $this->repository = $fieldGroup;
        $this->itemRepository = $fieldItem;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(FieldGroupsListDataTable $fieldGroupsListDataTable)
    {
        $this->setPageTitle('Custom fields');

        $this->dis['dataTable'] = $fieldGroupsListDataTable->run();

        return do_filter('custom-fields.index.get', $this)->viewAdmin('index');
    }

    /**
     * @param FieldGroupsListDataTable|BaseEngine $fieldGroupsListDataTable
     * @return \Illuminate\Http\JsonResponse
     */
    public function postListing(FieldGroupsListDataTable $fieldGroupsListDataTable)
    {
        $data = $fieldGroupsListDataTable->with($this->groupAction());

        return do_filter('datatables.custom-fields.index.post', $data, $this);
    }

    /**
     * Handle group actions
     * @return array
     */
    private function groupAction()
    {
        $data = [];
        if ($this->request->get('customActionType', null) == 'group_action') {

            if(!$this->userRepository->hasPermission($this->loggedInUser, ['edit-field-groups'])) {
                return [
                    'customActionMessage' => 'You do not have permission',
                    'customActionStatus' => 'danger',
                ];
            }

            $ids = (array)$this->request->get('id', []);

            $actionValue = $this->request->get('customActionValue');

            switch ($actionValue) {
                case 'deleted':
                    if(!$this->userRepository->hasPermission($this->loggedInUser, ['delete-field-groups'])) {
                        return [
                            'customActionMessage' => 'You do not have permission',
                            'customActionStatus' => 'danger',
                        ];
                    }
                    /**
                     * Delete pages
                     */
                    $result = $this->repository->delete($ids);
                    break;
                case 'activated':
                case 'diabled':
                    $result = $this->repository->updateMultiple($ids, [
                        'status' => $actionValue,
                    ], true);
                    break;
                default:
                    $result = [
                        'messages' => 'Method not allowed',
                        'error' => true
                    ];
                    break;
            }

            $data['customActionMessage'] = $result['messages'];
            $data['customActionStatus'] = $result['error'] ? 'danger' : 'success';

        }
        return $data;
    }

    public function postUpdateStatus($id, $status)
    {
        $data = [
            'status' => $status
        ];
        $result = $this->repository->updateFieldGroup($id, $data);
        return response()->json($result, $result['response_code']);
    }

    public function getCreate()
    {
        $this->setPageTitle('Create field group');
        $this->breadcrumbs->addLink('Create field group');

        $this->dis['currentId'] = 0;

        $this->dis['customFieldItems'] = json_encode([]);

        $this->dis['object'] = $this->repository->getModel();
        $oldInputs = old();
        if ($oldInputs) {
            foreach ($oldInputs as $key => $row) {
                if($key === 'customFieldItems') {
                    $this->dis['customFieldItems'] = $row;
                } else {
                    $this->dis['object']->$key = $row;
                }
            }
        }

        return do_filter('custom-fields.create.get', $this)->viewAdmin('create');
    }

    public function postCreate(CreateFieldGroupRequest $request)
    {
        $result = $this->repository->createFieldGroup(array_merge($request->except(['_token']), [
            'updated_by' => $this->loggedInUser->id,
        ]));

        $msgType = $result['error'] ? 'danger' : 'success';

        $this->flashMessagesHelper
            ->addMessages($result['messages'], $msgType)
            ->showMessagesOnSession();

        if ($result['error']) {
            return redirect()->back()->withInput();
        }

        do_action('custom-fields.after-create.post', null, $result, $this);

        if ($request->has('_continue_edit')) {
            return redirect()->to(route('admin::custom-fields.field-group.edit.get', ['id' => $result['data']->id]));
        }

        return redirect()->to(route('admin::custom-fields.index.get'));
    }

    public function getEdit($id)
    {
        $item = $this->repository->find($id);

        if (!$item) {
            $this->flashMessagesHelper
                ->addMessages('This field group not exists', 'danger')
                ->showMessagesOnSession();

            return redirect()->to(route('admin::custom-fields.field-group.edit.get'));
        }

        $this->setPageTitle('Edit field group', '#' . $id . ' ' . str_limit($item->name, 70));
        $this->breadcrumbs->addLink('Edit field group');

        $this->dis['object'] = $item;

        $this->dis['customFieldItems'] = json_encode($this->repository->getFieldGroupItems($id));

        return do_filter('custom-fields.edit.get', $this, $id)->viewAdmin('edit');
    }

    public function postEdit(UpdateFieldGroupRequest $request, $id)
    {
        $result = $this->repository->updateFieldGroup($id, array_merge($request->except(['_token']), [
            'updated_by' => $this->loggedInUser->id
        ]));

        $msgType = $result['error'] ? 'danger' : 'success';

        $this->flashMessagesHelper
            ->addMessages($result['messages'], $msgType)
            ->showMessagesOnSession();

        if ($result['error']) {
            return redirect()->back();
        }

        do_action('custom-fields.after-edit.post', $id, $result, $this);

        if ($request->has('_continue_edit')) {
            return redirect()->back();
        }

        return redirect()->to(route('admin::custom-fields.index.get'));
    }

    public function deleteDelete($id)
    {
        $result = $this->repository->deleteFieldGroup($id);

        do_action('custom-fields.after-delete.delete', $id, $result, $this);

        return response()->json($result, $result['response_code']);
    }
}
