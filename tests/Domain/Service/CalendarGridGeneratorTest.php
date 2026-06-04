<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Service\CalendarGridGenerator;
use PHPUnit\Framework\TestCase;

final class CalendarGridGeneratorTest extends TestCase
{
    private CalendarGridGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new CalendarGridGenerator();
    }

    public function testGenerateJune2026StartingOnMonday(): void
    {
        // June 2026: starts on Monday (2026-06-01), has 30 days
        $grid = $this->generator->generate(2026, 6, 'monday');

        $this->assertSame(2026, $grid->getYear());
        $this->assertSame(6, $grid->getMonth());
        $this->assertSame('June', $grid->getMonthName());
        $this->assertSame('monday', $grid->getStartDayOfWeek());
        $this->assertSame(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], $grid->getHeaders());

        $weeks = $grid->getWeeks();
        // 30 days starting on Monday:
        // Week 1: 1..7 (no padding)
        // Week 2: 8..14
        // Week 3: 15..21
        // Week 4: 22..28
        // Week 5: 29..30 + 5 padding days (July 1..5)
        $this->assertCount(5, $weeks);

        // Check first week
        $firstWeek = $weeks[0];
        $this->assertCount(7, $firstWeek);
        $this->assertSame(1, $firstWeek[0]->getDay());
        $this->assertTrue($firstWeek[0]->isCurrentMonth());
        $this->assertSame('2026-06-01', $firstWeek[0]->getDate()->format('Y-m-d'));

        // Check last week
        $lastWeek = $weeks[4];
        $this->assertCount(7, $lastWeek);
        $this->assertSame(29, $lastWeek[0]->getDay());
        $this->assertTrue($lastWeek[0]->isCurrentMonth());
        
        $this->assertSame(30, $lastWeek[1]->getDay());
        $this->assertTrue($lastWeek[1]->isCurrentMonth());

        $this->assertSame(1, $lastWeek[2]->getDay());
        $this->assertFalse($lastWeek[2]->isCurrentMonth());
        $this->assertSame('2026-07-01', $lastWeek[2]->getDate()->format('Y-m-d'));
        
        $this->assertSame(5, $lastWeek[6]->getDay());
        $this->assertFalse($lastWeek[6]->isCurrentMonth());
    }

    public function testGenerateJune2026StartingOnSunday(): void
    {
        // June 2026 starting on Sunday.
        // First day of June is Monday.
        // So Sunday, May 31 will be the first cell (padding).
        $grid = $this->generator->generate(2026, 6, 'sunday');

        $this->assertSame('sunday', $grid->getStartDayOfWeek());
        $this->assertSame(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], $grid->getHeaders());

        $weeks = $grid->getWeeks();
        // Week 1: May 31 (padding), June 1..6
        // Week 2: June 7..13
        // Week 3: June 14..20
        // Week 4: June 21..27
        // Week 5: June 28..30, July 1..4 (padding)
        $this->assertCount(5, $weeks);

        // Check first week first cell (May 31)
        $firstWeek = $weeks[0];
        $this->assertSame(31, $firstWeek[0]->getDay());
        $this->assertFalse($firstWeek[0]->isCurrentMonth());
        $this->assertSame('2026-05-31', $firstWeek[0]->getDate()->format('Y-m-d'));

        // Check first week second cell (June 1)
        $this->assertSame(1, $firstWeek[1]->getDay());
        $this->assertTrue($firstWeek[1]->isCurrentMonth());

        // Check last week
        $lastWeek = $weeks[4];
        $this->assertSame(30, $lastWeek[2]->getDay());
        $this->assertTrue($lastWeek[2]->isCurrentMonth());
        
        $this->assertSame(1, $lastWeek[3]->getDay());
        $this->assertFalse($lastWeek[3]->isCurrentMonth());
        $this->assertSame('2026-07-01', $lastWeek[3]->getDate()->format('Y-m-d'));

        $this->assertSame(4, $lastWeek[6]->getDay());
        $this->assertFalse($lastWeek[6]->isCurrentMonth());
    }
}
