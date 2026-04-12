<?php

namespace App\Traits;

use Carbon\CarbonInterface;
use Carbon\Carbon;

trait HasNepaleseDate
{
    public function toFullBsDate(CarbonInterface $date): string
    {
        $enYear = (int)$date->format('Y');

        // Reference Data: Har saal ke mahino (Baisakh to Chaitra) ke exact din.
        // Ye data Nepal ke official calendar se liya gaya hai.
        $calendarData = [
            2080 => [31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30],
            2081 => [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
            2082 => [30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30],
            2083 => [31, 31, 32, 31, 31, 30, 30, 30, 29, 30, 30, 30],
            2084 => [31, 31, 32, 31, 31, 31, 30, 29, 30, 30, 30, 30],
            2085 => [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30],
            2086 => [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30],
            2087 => [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 31],
            2088 => [30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30],
            2089 => [31, 31, 32, 31, 31, 30, 30, 30, 29, 30, 30, 30],
            2090 => [31, 31, 32, 31, 31, 31, 30, 29, 30, 30, 30, 30],
        ];

        $bsMonthNames = [
            1 => 'Baisakh',
            2 => 'Jestha',
            3 => 'Ashadh',
            4 => 'Shrawan',
            5 => 'Bhadra',
            6 => 'Ashwin',
            7 => 'Kartik',
            8 => 'Mangsir',
            9 => 'Poush',
            10 => 'Magh',
            11 => 'Falgun',
            12 => 'Chaitra'
        ];

        // Reference Point: BS 2080-01-01 is AD 2023-04-14
        $referenceDate = Carbon::create(2023, 4, 14)->startOfDay();
        $targetDate = Carbon::instance($date)->startOfDay();

        // Agar purani date hai toh normal format return karein
        if ($targetDate->lt($referenceDate)) {
            return $date->format('d M Y');
        }

        $diffInDays = $referenceDate->diffInDays($targetDate);

        $bsYear = 2080;
        $bsMonth = 1;
        $bsDay = 1;

        // Loop tab tak jab tak saare din adjust na ho jayein
        while ($diffInDays > 0) {
            // Check karein ki hamare paas us saal ka data hai ya nahi
            if (!isset($calendarData[$bsYear])) {
                break; // Data khatam hone par loop rokein
            }

            $daysInMonth = $calendarData[$bsYear][$bsMonth - 1];

            if ($diffInDays >= $daysInMonth) {
                $diffInDays -= $daysInMonth;
                $bsMonth++;
                if ($bsMonth > 12) {
                    $bsMonth = 1;
                    $bsYear++;
                }
            } else {
                $bsDay += $diffInDays;
                $diffInDays = 0;
            }
        }

        return "{$bsDay} {$bsMonthNames[$bsMonth]} {$bsYear}";
    }
}
