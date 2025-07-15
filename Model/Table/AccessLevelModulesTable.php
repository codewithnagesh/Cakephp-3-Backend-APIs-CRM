<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AccessLevelModules Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ParentAccessLevelModules
 * @property \Cake\ORM\Association\HasMany $ChildAccessLevelModules
 * @property \Cake\ORM\Association\HasMany $AccessLevelPermissions
 *
 * @method \App\Model\Entity\AccessLevelModule get($primaryKey, $options = [])
 * @method \App\Model\Entity\AccessLevelModule newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AccessLevelModule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AccessLevelModule|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AccessLevelModule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AccessLevelModule[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AccessLevelModule findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AccessLevelModulesTable extends Table
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

        $this->setTable('access_level_modules');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree', [
            'recoverOrder' => ['lft' => 'ASC'],
        ]);

        $this->belongsTo('ParentAccessLevelModules', [
            'className' => 'AccessLevelModules',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildAccessLevelModules', [
            'className' => 'AccessLevelModules',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('AccessLevelPermissions', [
            'foreignKey' => 'access_level_module_id',
            'dependent' => true
        ]);
        $this->hasOne('AccessLevelModuleDealerGroups', [
            'foreignKey' => 'access_level_module_id'
        ]);

        $this->belongsToMany('DealerGroups', [
            'foreignKey' => 'access_level_module_id',
            'targetForeignKey' => 'dealer_group_id',
            'joinTable' => 'access_level_module_dealer_groups',
            'dependent' => true
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

        $validator
            ->allowEmptyString('module_name');

        $validator
            ->integer('module_type')
            ->requirePresence('module_type', 'create')
            ->notEmptyString('module_type');

        return $validator;
    }

}
