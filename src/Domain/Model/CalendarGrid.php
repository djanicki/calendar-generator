<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class CalendarGrid
{
    /**
     * @param array<int, array<int, CalendarDay>> $weeks
     * @param array<int, string> $headers
     */
    public function __construct(
        private readonly int $year,
        private readonly int $month,
        private readonly string $monthName,
        private readonly string $startDayOfWeek,
        private readonly array $headers,
        private readonly array $weeks
    ) {}

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getMonthName(): string
    {
        return $this->monthName;
    }

    public function getStartDayOfWeek(): string
    {
        return $this->startDayOfWeek;
    }

    /**
     * @return array<int, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array<int, array<int, CalendarDay>>
     */
    public function getWeeks(): array
    {
        return $this->weeks;
    }
}
