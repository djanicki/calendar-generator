<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Model\CalendarGrid;
use App\Domain\Service\CalendarGridGenerator;

final class CalendarGenerationService
{
    public function __construct(
        private readonly CalendarGridGenerator $gridGenerator
    ) {}

    public function generateCalendar(int $year, int $month, string $startDayOfWeek): CalendarGrid
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Month must be between 1 and 12.');
        }
        if ($year < 1) {
            throw new \InvalidArgumentException('Year must be positive.');
        }
        
        $startDayOfWeek = strtolower($startDayOfWeek);
        if ($startDayOfWeek !== 'monday' && $startDayOfWeek !== 'sunday') {
            throw new \InvalidArgumentException('Start day of week must be either monday or sunday.');
        }

        return $this->gridGenerator->generate($year, $month, $startDayOfWeek);
    }
}
