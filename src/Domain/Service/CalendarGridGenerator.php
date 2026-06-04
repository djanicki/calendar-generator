<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\CalendarDay;
use App\Domain\Model\CalendarGrid;

final class CalendarGridGenerator
{
    public function generate(int $year, int $month, string $startDayOfWeek): CalendarGrid
    {
        // 1. Determine headers
        $startDayOfWeek = strtolower($startDayOfWeek);
        if ($startDayOfWeek === 'sunday') {
            $headers = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $startW = 0; // Sunday
        } else {
            $headers = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $startW = 1; // Monday
        }

        // 2. Dates calculations
        $firstDayOfMonth = new \DateTimeImmutable(sprintf('%d-%02d-01 00:00:00', $year, $month));
        $monthName = $firstDayOfMonth->format('F');
        
        // Day of week of the 1st of the month (0 = Sunday, 1 = Monday, etc.)
        $firstDayW = (int) $firstDayOfMonth->format('w');
        
        // Calculate starting padding days
        $paddingStart = ($firstDayW - $startW + 7) % 7;
        
        $daysInMonth = (int) $firstDayOfMonth->format('t');
        
        $days = [];
        
        // Add padding from previous month
        if ($paddingStart > 0) {
            $prevMonthDate = $firstDayOfMonth->modify('-1 month');
            $prevMonthDays = (int) $prevMonthDate->format('t');
            $prevMonthYear = (int) $prevMonthDate->format('Y');
            $prevMonthNum = (int) $prevMonthDate->format('n');
            
            for ($i = $paddingStart; $i > 0; $i--) {
                $dayNum = $prevMonthDays - $i + 1;
                $date = new \DateTimeImmutable(sprintf('%d-%02d-%02d 00:00:00', $prevMonthYear, $prevMonthNum, $dayNum));
                $days[] = new CalendarDay($dayNum, false, $date);
            }
        }
        
        // Add days of current month
        for ($dayNum = 1; $dayNum <= $daysInMonth; $dayNum++) {
            $date = new \DateTimeImmutable(sprintf('%d-%02d-%02d 00:00:00', $year, $month, $dayNum));
            $days[] = new CalendarDay($dayNum, true, $date);
        }
        
        // Calculate ending padding days
        $totalCells = count($days);
        $paddingEnd = (7 - ($totalCells % 7)) % 7;
        
        if ($paddingEnd > 0) {
            $nextMonthDate = $firstDayOfMonth->modify('+1 month');
            $nextMonthYear = (int) $nextMonthDate->format('Y');
            $nextMonthNum = (int) $nextMonthDate->format('n');
            
            for ($dayNum = 1; $dayNum <= $paddingEnd; $dayNum++) {
                $date = new \DateTimeImmutable(sprintf('%d-%02d-%02d 00:00:00', $nextMonthYear, $nextMonthNum, $dayNum));
                $days[] = new CalendarDay($dayNum, false, $date);
            }
        }
        
        // Chunk into weeks of 7 days
        $weeks = array_chunk($days, 7);
        
        return new CalendarGrid($year, $month, $monthName, $startDayOfWeek, $headers, $weeks);
    }
}
