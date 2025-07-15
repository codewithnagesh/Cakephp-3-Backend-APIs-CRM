<?php
namespace App\Utility;

/**
 * Utility class to format various data reports.
 */
class ReportFormatter
{
    /**
     * Formats user report for console display.
     *
     * @param array $users
     * @return string
     */
    public static function formatUserReport(array $users): string
    {
        $output = "Inactive User Report:\n";
        foreach ($users as $user) {
            $output .= "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Last Login: {$user->last_login}\n";
        }
        return $output;
    }

    /**
     * Formats never-logged-in users.
     *
     * @param array $users
     * @return string
     */
    public static function formatNeverLoggedInReport(array $users): string
    {
        $output = "Never Logged-In Users:\n";
        foreach ($users as $user) {
            $output .= "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Created: {$user->created}\n";
        }
        return $output;
    }

    /**
     * Formats frequent users report.
     *
     * @param array $users
     * @return string
     */
    public static function formatFrequentUsers(array $users): string
    {
        $output = "Frequent Users (Last 7 Days):\n";
        foreach ($users as $user) {
            $output .= "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Last Login: {$user->last_login}\n";
        }
        return $output;
    }
}
