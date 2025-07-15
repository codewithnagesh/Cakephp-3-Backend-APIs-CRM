<?php

namespace App\Controller;

use App\Utility\AccessLevelPermission;

/**
 * PermissionModule Controller
 *
 * @property \App\Model\Table\PermissionModuleTable $PermissionModuleTable
 */
class PermissionModuleController extends AppController {
    /**
     * Init method
     *
     * @throws \Exception
     */
    public function initialize() {
        parent::initialize();
        $this->loadComponent('ApiValidation');
    }

    /**
     * Index method
     *
     * @return null
     */
    public function index() {
        // Check Acl Permission for the acl_module_id = 66.
        $aclAccessTypesLead = AccessLevelPermission::CheckModulePermission($this->Auth->user(), 66);

        $dealerGroupId = $this->Auth->user('dealer_group_id');
        $groupModulesData = [];
        $groupId = $this->request->getQuery('group_id');

        // Get all Groups
        $this->loadModel('Groups');
        $groups = $this->Groups->find()
            ->enableHydration(false)
            ->where(['dealer_group_id IN' => $dealerGroupId])
            ->select(['id','name'])
            ->toArray();

        // Get all Access type
        $this->loadModel('AclAccessTypes');
        $aclAcessTypes = $this->AclAccessTypes->find('list')
            ->toArray();

        $groupModulesData['groups'] = $groups;
        $groupModulesData['groupModules'] = $groupModules;
        $groupModulesData['aclAccessTypes'] = $aclAcessTypes;

        $this->set(compact('groupModulesData'));
        $this->set('_serialize', ['groupModulesData']);
    }

    /**
     * Allow module permission method
     */
    public function allow() {
        if ($this->request->is(['patch', 'post', 'put'])) {

            $groupId = $this->request->getData('group_id');
            $moduleId = $this->request->getData('module_id');
            $moduleType = $this->request->getData('module_type');
            $moduleType === 2 ? $accessType = 1 : $accessType = 0;

            // Get already permitted modules
            $this->loadModel('AccessLevelPermissions');
            $permittedModuleIds = $this->AccessLevelPermissions->find()
                ->where(['group_id' => $groupId])
                ->extract('acl_module_id')
                ->toArray();

            // Get child of the requested module
            $requestChildModules = $this->AclModules->find('children', ['for' => $moduleId])
                ->select(['id', 'module_type'])
                ->where(['id NOT IN' => $permittedModuleIds ? $permittedModuleIds : [0]])
                ->toArray();

            if (!empty($saveData)) {
                $entities = $this->AccessLevelPermissions->newEntities($saveData);
                $accessLevelPermissions = $this->AccessLevelPermissions->saveMany($entities);
                if ($accessLevelPermissions) {
                    $message = __('Group permission has been saved successfully.');
                } else {
                    $message = __('Group permission could not be saved. Please, try again.');
                }
            }
        }

        $this->set([
            'message' => $message,
            'accessLevelPermissions' => $accessLevelPermissions,
            '_serialize' => ['message', 'accessLevelPermissions'],
        ]);
    }

    /**
     * Update access type method
     */
    public function UpdateAccessType() {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $aclAccessTypesLead = AccessLevelPermission::ChkPermission($this->Auth->user(), 66);

            $groupId = $this->request->getData('group_id');
            $moduleId = $this->request->getData('module_id');
            $accessType = $this->request->getData('access_type');

            $this->ApiValidation->validateUserGroupId($groupId);

            $this->loadModel('AccessLevelPermissions');
            $accessLevelPermission = $this->AccessLevelPermissions->find()
                ->where(['acl_module_id' => $moduleId, 'group_id' => $groupId])
                ->first();

            $accessLevelPermission->acl_access_type_id = $accessType;

            if ($this->AccessLevelPermissions->save($accessLevelPermission)) {
                $message = __('Access Type has been Updated successfully.');
            } else {
                $message = __('Access Type could not be Updated. Please, try again.');
            }

            $this->set([
                'message' => $message,
                '_serialize' => ['message'],
            ]);
        }
    }
}
