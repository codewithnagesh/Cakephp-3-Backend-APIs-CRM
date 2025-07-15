<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AccessLevelPermissions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Groups
 * @property \Cake\ORM\Association\BelongsTo $AclModules
 * @property \Cake\ORM\Association\BelongsTo $AclAccessTypes
 *
 * @method \App\Model\Entity\AccessLevelPermission get($primaryKey, $options = [])
 * @method \App\Model\Entity\AccessLevelPermission newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AccessLevelPermission[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AccessLevelPermission|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AccessLevelPermission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AccessLevelPermission[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AccessLevelPermission findOrCreate($search, callable $callback = null)
 */
class AccessLevelPermissionsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('acl_permissions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id'
        ]);
        $this->belongsTo('AclModules', [
            'foreignKey' => 'acl_module_id'
        ]);
        $this->belongsTo('AclAccessTypes', [
            'foreignKey' => 'acl_access_type_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        $rules->add($rules->existsIn(['acl_module_id'], 'AclModules'));
        $rules->add($rules->isUnique(['acl_module_id', 'group_id']));

        return $rules;
    }
}
