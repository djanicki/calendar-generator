<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class CalendarDay
{
    public function __construct(
        private readonly int $day,
        private readonly bool $isCurrentMonth,
        private readonly \DateTimeImmutable $date
    ) {}

    public function getDay(): int
    {
        return $this->day;
    }

    public function isCurrentMonth(): bool
    {
        return $this->isCurrentMonth;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}
