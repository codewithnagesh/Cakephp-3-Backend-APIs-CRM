<?php
namespace App\Service;

use Cake\Datasource\FactoryLocator;
use Cake\Log\Log;

/**
 * Service to handle user reporting operations.
 */
class UserReportService
{
    /**
     * Generates a report of users inactive for a given number of days.
     *
     * @param int $days
     * @return array
     */
    public function generateInactiveUsersReport(int $days): array
    {
        $usersTable = FactoryLocator::get('Table')->get('Users');
        $dateThreshold = new \DateTime("-{$days} days");

        $users = $usersTable->find()
            ->where(['Users.last_login <' => $dateThreshold])
            ->select(['id', 'name', 'email', 'last_login'])
            ->toArray();

        Log::info("Generated report for inactive users since {$dateThreshold->format('Y-m-d')}");

        return $users;
    }

    /**
     * Returns a report of users who never logged in since account creation.
     *
     * @return array
     */
    public function getNeverLoggedInUsers(): array
    {
        $usersTable = FactoryLocator::get('Table')->get('Users');
        $users = $usersTable->find()
            ->where(['Users.last_login IS' => null])
            ->select(['id', 'name', 'email', 'created'])
            ->toArray();

        Log::info("Fetched users who never logged in.");

        return $users;
    }

    /**
     * Fetch users who logged in frequently in the last 7 days.
     *
     * @return array
     */
    public function getFrequentUsers(): array
    {
        $usersTable = FactoryLocator::get('Table')->get('Users');
        $weekAgo = new \DateTime('-7 days');

        $users = $usersTable->find()
            ->where(['Users.last_login >=' => $weekAgo])
            ->select(['id', 'name', 'email', 'last_login'])
            ->order(['Users.last_login' => 'DESC'])
            ->toArray();

        Log::info("Fetched frequent users logged in last 7 days.");

        return $users;
    }
}
