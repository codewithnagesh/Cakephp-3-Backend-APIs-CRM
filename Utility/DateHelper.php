<?php
namespace App\Utility;

/**
 * Utility class for date operations and formatting.
 */
class DateHelper
{
    /**
     * Returns a date X days ago.
     *
     * @param int $days
     * @return string
     */
    public static function daysAgo(int $days): string
    {
        $date = new \DateTime("-{$days} days");
        return $date->format('Y-m-d');
    }

    /**
     * Compares two dates and returns the difference in days.
     *
     * @param string $date1
     * @param string $date2
     * @return int
     */
    public static function daysBetween(string $date1, string $date2): int
    {
        $d1 = new \DateTime($date1);
        $d2 = new \DateTime($date2);
        return $d1->diff($d2)->days;
    }

    /**
     * Checks if a date is a weekend.
     *
     * @param string $date
     * @return bool
     */
    public static function isWeekend(string $date): bool
    {
        $day = (new \DateTime($date))->format('N');
        return in_array($day, [6, 7]);
    }
}
