<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Calendar;

interface CalendarRepositoryInterface
{
    public function save(Calendar $calendar): void;

    public function findByToken(string $token): ?Calendar;
}
