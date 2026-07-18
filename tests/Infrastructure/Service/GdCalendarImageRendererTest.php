<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Service;

use App\Domain\Model\CalendarDay;
use App\Domain\Model\CalendarGrid;
use App\Infrastructure\Service\GdCalendarImageRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GdCalendarImageRendererTest extends TestCase
{
    private string $fontDirectory;
    private string $tempOutputDir;

    protected function setUp(): void
    {
        $this->fontDirectory = dirname(__DIR__, 3) . '/assets/fonts';
        $this->tempOutputDir = sys_get_temp_dir() . '/calendar_test_' . uniqid('', true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempOutputDir)) {
            $files = glob($this->tempOutputDir . '/*');
            if ($files !== false) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            rmdir($this->tempOutputDir);
        }
    }

    public function testRenderTranslatesMonthAndHeadersWhenTranslatorProvided(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::atLeastOnce())
            ->method('trans')
            ->willReturnCallback(function (string $id): string {
                return match ($id) {
                    'months.june' => 'Czerwiec',
                    'days.mon' => 'Pn',
                    'days.tue' => 'Wt',
                    'days.wed' => 'Śr',
                    'days.thu' => 'Cz',
                    'days.fri' => 'Pt',
                    'days.sat' => 'Sb',
                    'days.sun' => 'Nd',
                    default => $id,
                };
            });

        $renderer = new GdCalendarImageRenderer($this->fontDirectory, $translator);

        $weeks = [
            array_map(
                fn (int $d) => new CalendarDay($d, true, new \DateTimeImmutable(sprintf('2026-06-%02d', $d))),
                range(1, 7)
            )
        ];

        $grid = new CalendarGrid(
            2026,
            6,
            'June',
            'monday',
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            $weeks
        );

        $filename = $renderer->render($grid, $this->tempOutputDir, 'modern');

        $this->assertNotEmpty($filename);
        $this->assertFileExists($this->tempOutputDir . '/' . $filename);
        $this->assertGreaterThan(0, filesize($this->tempOutputDir . '/' . $filename));
    }

    public function testRenderWorksWithoutTranslator(): void
    {
        $renderer = new GdCalendarImageRenderer($this->fontDirectory, null);

        $weeks = [
            array_map(
                fn (int $d) => new CalendarDay($d, true, new \DateTimeImmutable(sprintf('2026-06-%02d', $d))),
                range(1, 7)
            )
        ];

        $grid = new CalendarGrid(
            2026,
            6,
            'June',
            'monday',
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            $weeks
        );

        $filename = $renderer->render($grid, $this->tempOutputDir, 'classic');

        $this->assertNotEmpty($filename);
        $this->assertFileExists($this->tempOutputDir . '/' . $filename);
        $this->assertGreaterThan(0, filesize($this->tempOutputDir . '/' . $filename));
    }
}
