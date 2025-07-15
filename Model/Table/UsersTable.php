<?php
namespace App\Model\Table;

use App\Utility\PoolDbConnection;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Groups Model
 *
 * @property \Cake\ORM\Association\HasMany $Users
 */

class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        //Set DB Pool
        $pool_config = ConnectionManager::get('default');
        $this->setConnection($pool_config);
        $this->setTable($pool_config->config()['database'] . '.users');

        $this->setDisplayField('first_name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->hasMany('DealerUsers', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsToMany('Dealers', [
            'joinTable' => 'dealer_users',
            'dependent' => true,
            'targetForeignKey' => 'dealer_id',
            'foreignKey' => 'user_id',
            'conditions' => ['DealerUsers.is_active' => '1'],
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
        ]);
        $this->hasMany('VrUserGroupUsers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('VrUserGroupBdc', [
            'foreignKey' => 'bdc_user_id',
        ]);
        $this->hasOne('OnlineClients', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasOne('UserStatuses', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasOne('UserSettings', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasOne('UserDefaultSearch', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Histories', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('ContactLeads', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('LeadWatchers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('ContactLeadEvents', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('DealerUserHours', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('AutoresponderRules', [
            'foreignKey' => 'created_by',
        ]);
        $this->hasMany('UserNotificationSettings', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('UserToastNotifications', [
            'foreignKey' => 'user_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('username', 'create')
            ->notEmptyString('username')
            ->add('username', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Username already exists, please use a different username.',
            ]);

        $validator
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        $validator
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->add('email', 'validFormat', [
                'rule' => 'email',
                'message' => 'E-mail must be valid',
            ])
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Email already exists, please use a different email.',
            ]);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->isUnique(['username']));
        //$rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        $rules->add($rules->existsIn(['dealer_id'], 'Dealers'));

        return $rules;
    }

    public function afterSave($event, $entity, $options) {

        if ($entity->is_active == 0) {
            //Remove Email Notifications which belongs to inactive user.
            $notificationEmailsTable = TableRegistry::getTableLocator()->get('NotificationEmails');
            $notificationEmailsTable->deleteAll(['OR' => ['email' => $entity->email, 'user_id' => $entity->id]]);

            $dealerUsersTable = TableRegistry::getTableLocator()->get('DealerUsers');

            $dealer_id = $dealerUsersTable->find()
                            ->where(['user_id' =>  $entity->id])
                            ->extract('dealer_id')
                            ->first();
            PoolDbConnection::set($dealer_id, null);

            $rptUserReportNotificationsTable = TableRegistry::getTableLocator()->get('RptUserReportNotifications');
            $rptUserReportNotificationsTable->deleteAll(['email' => $entity->email]);
        }
    }
}
