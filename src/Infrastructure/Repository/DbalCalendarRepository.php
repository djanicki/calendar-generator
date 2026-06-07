<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Calendar;
use App\Domain\Repository\CalendarRepositoryInterface;
use Doctrine\DBAL\Connection;

final class DbalCalendarRepository implements CalendarRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function save(Calendar $calendar): void
    {
        $this->connection->executeStatement(
            'INSERT INTO calendars (token, sel_month, sel_year, monday_first, generated_file, expires_at)
             VALUES (UNHEX(REPLACE(:token, \'-\', \'\')), :sel_month, :sel_year, :monday_first, :generated_file, :expires_at)',
            [
                'token' => $calendar->getToken(),
                'sel_month' => $calendar->getSelMonth(),
                'sel_year' => $calendar->getSelYear(),
                'monday_first' => $calendar->isMondayFirst() ? 1 : 0,
                'generated_file' => $calendar->getGeneratedFile(),
                'expires_at' => $calendar->getExpiresAt()->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function findByToken(string $token): ?Calendar
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, LOWER(HEX(token)) AS token_hex, sel_month, sel_year, monday_first, generated_file, expires_at, created_at
             FROM calendars
             WHERE token = UNHEX(REPLACE(:token, \'-\', \'\'))',
            ['token' => $token]
        );

        if ($row === false) {
            return null;
        }

        $tokenHex = $row['token_hex'];
        $formattedToken = sprintf(
            '%s-%s-%s-%s-%s',
            substr($tokenHex, 0, 8),
            substr($tokenHex, 8, 4),
            substr($tokenHex, 12, 4),
            substr($tokenHex, 16, 4),
            substr($tokenHex, 20, 12)
        );

        return new Calendar(
            token: $formattedToken,
            selMonth: (int) $row['sel_month'],
            selYear: (int) $row['sel_year'],
            mondayFirst: (bool) $row['monday_first'],
            generatedFile: $row['generated_file'],
            expiresAt: new \DateTimeImmutable($row['expires_at']),
            createdAt: new \DateTimeImmutable($row['created_at']),
            id: (int) $row['id'],
        );
    }
}
