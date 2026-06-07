<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Model\Calendar;
use App\Domain\Model\CalendarGrid;
use App\Domain\Repository\CalendarRepositoryInterface;
use App\Domain\Service\CalendarGridGenerator;
use App\Domain\Service\CalendarImageRendererInterface;
use Symfony\Component\Uid\Uuid;

final class CalendarGenerationService
{
    public function __construct(
        private readonly CalendarGridGenerator $gridGenerator,
        private readonly CalendarImageRendererInterface $imageRenderer,
        private readonly CalendarRepositoryInterface $calendarRepository,
        private readonly string $generatedCalendarsDir
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

    public function generateAndPersist(int $year, int $month, string $startDayOfWeek): Calendar
    {
        $grid = $this->generateCalendar($year, $month, $startDayOfWeek);

        $filename = $this->imageRenderer->render($grid, $this->generatedCalendarsDir);

        $token = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();

        $calendar = new Calendar(
            token: $token,
            selMonth: $month,
            selYear: $year,
            mondayFirst: strtolower($startDayOfWeek) === 'monday',
            generatedFile: $filename,
            expiresAt: $now->modify('+1 hour'),
            createdAt: $now,
        );

        $this->calendarRepository->save($calendar);

        return $calendar;
    }
}
