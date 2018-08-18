<?php
/**
 * This file is the model file of the application. Used for
 *  management departments.
 *
 * InternalPhonebook: Internal phone book based on content of Active Directory.
 * @copyright Copyright 2017, Andrey Klimov.
 * @package app.Model
 */

App::uses('DepartmentDb', 'CakeLdap.Model');
App::uses('ClassRegistry', 'Utility');

/**
 * The model is used for management departments.
 *
 * @package app.Model
 */
class Department extends DepartmentDb
{

    /**
     * hasOne associations
     *
     * @var array
     */
    public $hasOne = [
        'DepartmentExtension' => [
            'className' => 'DepartmentExtension',
            'foreignKey' => 'department_id',
            'dependent' => true,
            'fields' => [
                'DepartmentExtension.id',
                'DepartmentExtension.name',
            ]
        ],
    ];

    /**
     * Called after each successful save operation.
     *
     * Actions:
     *  - Creating new record extension for department;
     *  - Clear cache.
     *
     * @param bool $created True if this save created a new record
     * @param array $options Options passed from Model::save().
     * @return void
     * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
     * @see Model::save()
     */
    public function afterSave($created, $options = [])
    {
        parent::afterSave($created, $options);
        Cache::clear(false, CACHE_KEY_DEPARTMENTS_LOCAL_INFO);
        if (!$created || (isset($this->data['DepartmentExtension']['name']) &&
            !empty($this->data['DepartmentExtension']['name']))) {
            return;
        }

        $dataToSave = [
            'name' => $this->data[$this->alias]['value'],
            'id' => $this->data[$this->alias]['id'],
        ];
        $this->DepartmentExtension->createDepartmentExtension($dataToSave);
    }

    /**
     * Called before every deletion operation.
     *
     * Actions:
     *  - Allow to delete only blocked department.
     *
     * @param bool $cascade If true records that depend on this record will also be deleted
     * @return bool True if the operation should continue, false if it should abort
     * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforedelete
     */
    public function beforeDelete($cascade = true)
    {
        $blockExists = $this->hasField('block');
        if ($blockExists) {
            $block = $this->field('block');
            if (!$block) {
                return false;
            }
        }

        return true;
    }

    /**
     * Called after every deletion operation.
     *
     * Actions:
     *  - Clear cache.
     *
     * @return void
     * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterdelete
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Cache::clear(false, CACHE_KEY_DEPARTMENTS_LOCAL_INFO);
    }

    /**
     * Return name of department by record ID.
     *
     * @param int|string $id The ID of the record to read.
     * @return array Name of department
     */
    public function get($id = null)
    {
        if (empty($id)) {
            return false;
        }

        $fields = [
            $this->alias . '.id',
            $this->alias . '.value',
            'DepartmentExtension.id',
            'DepartmentExtension.department_id',
            'DepartmentExtension.parent_id',
            'DepartmentExtension.lft',
            'DepartmentExtension.rght',
            'DepartmentExtension.name',
        ];
        $blockExists = $this->hasField('block');
        if ($blockExists) {
             $fields[] = $this->alias . '.block';
        }
        $conditions = [
            $this->alias . '.id' => $id
        ];
        $contain = [
            'DepartmentExtension'
        ];

        return $this->find('first', compact('fields', 'conditions', 'contain'));
    }

    /**
     * Return list of departments include extension
     *
     * @param int|string $limit Limit for result
     * @return array Return list of departments.
     */
    public function getListDepartmentsWithExtension($limit = CAKE_LDAP_SYNC_AD_LIMIT)
    {
        $limit = (int)$limit;
        $cachePath = 'list_departments_' . md5($limit);
        $cached = Cache::read($cachePath, CACHE_KEY_DEPARTMENTS_LOCAL_INFO);
        if ($cached !== false) {
            return $cached;
        }

        $result = [];
        $conditions = [];
        $fields = [
            $this->alias . '.id',
            $this->alias . '.value',
            'DepartmentExtension.name',
        ];
        $order = [
            'DepartmentExtension.name' => 'asc',
            $this->alias . '.value' => 'asc',
        ];
        $contain = [
            'DepartmentExtension',
        ];
        $data = $this->find('all', compact('fields', 'conditions', 'order', 'contain', 'limit'));
        if (empty($data)) {
            return $result;
        }

        foreach ($data as $dataItem) {
            if (!empty($dataItem['DepartmentExtension']['name']) &&
                ($dataItem[$this->alias]['value'] !== $dataItem['DepartmentExtension']['name'])) {
                $departmentName = $dataItem['DepartmentExtension']['name'] . ' (' . $dataItem[$this->alias]['value'] . ')';
            } else {
                $departmentName = $dataItem[$this->alias]['value'];
            }
            $result[$dataItem[$this->alias]['value']] = $departmentName;
        }
        Cache::write($cachePath, $result, CACHE_KEY_DEPARTMENTS_LOCAL_INFO);

        return $result;
    }

    /**
     * Put task of renaming department in the queue
     *
     * @param string $oldName Old name of department
     * @param string $newName New name of department
     * @param int $userRole Bit mask of user role
     * @param int $userId User ID, initiating the process
     * @param bool $useLdap If True, use information from LDAP server.
     *  Otherwise use database.
     * @return bool|array Return False on failure. Otherwise, return array of
     *   created Job containing id, data, ...
     */
    public function putRenameDepartmentTask($oldName = null, $newName = null, $userRole = null, $userId = null, $useLdap = false)
    {
        if (empty($oldName) || empty($newName)) {
            return false;
        }
        $data = [$this->alias => ['value' => $newName]];
        $this->set($data);
        if (!$this->validates()) {
            return false;
        }

        $taskParam = compact('oldName', 'newName', 'userRole', 'userId', 'useLdap');
        $modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');

        return $modelExtendQueuedTask->createJob('RenameDepartment', $taskParam, null, 'change');
    }

    /**
     * Renaming department for all employees of this department
     *
     * @param string $oldName Old name of department
     * @param string $newName New name of department
     * @param int $userRole Bit mask of user role
     * @param int $userId User ID, initiating the process
     * @param bool $useLdap If True, use information from LDAP server.
     *  Otherwise use database.
     * @return bool Success
     */
    public function renameDepartment($oldName = null, $newName = null, $userRole = null, $userId = null, $useLdap = false)
    {
        if (empty($oldName) || empty($newName)) {
            return false;
        }

        set_time_limit(TASK_RENAME_DEPARTMENT_TIME_LIMIT);
        $data = [$this->alias => ['value' => $newName]];
        $this->set($data);
        if (!$this->validates()) {
            return false;
        }

        if (!empty($userId) && (PHP_SAPI === 'cli')) {
            CakeSession::write('Auth.User.id', $userId);
        }

        $modelEmployeeEdit = ClassRegistry::init('EmployeeEdit');
        $listEmployees = $modelEmployeeEdit->getListEmployeesByDepartmentName($oldName);
        if (empty($listEmployees)) {
            return false;
        }

        $result = true;
        foreach ($listEmployees as $employeeDn) {
            if ($modelEmployeeEdit->changeDepartment($employeeDn, $newName, $userRole, $useLdap) === false) {
                $result = false;
            }
        }

        return $result;
    }
}
