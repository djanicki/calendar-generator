<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Model\CalendarGrid;
use App\Domain\Service\CalendarImageRendererInterface;

final class GdCalendarImageRenderer implements CalendarImageRendererInterface
{
    private const int IMAGE_WIDTH = 2970;
    private const int IMAGE_HEIGHT = 2100;

    // Padding
    private const int PADDING_X = 150;
    private const int PADDING_TOP = 150;

    // Colors (RGB)
    private const array COLOR_BG = [255, 255, 255];
    private const array COLOR_TEXT = [15, 23, 42];        // #0f172a
    private const array COLOR_HEADER_TEXT = [71, 85, 105]; // #475569
    private const array COLOR_OTHER_MONTH = [203, 213, 225]; // #cbd5e1
    private const array COLOR_YEAR = [100, 116, 139];      // #64748b
    private const array COLOR_BORDER = [226, 232, 240];     // #e2e8f0

    // Font sizes (in points)
    private const float FONT_SIZE_MONTH = 72.0;
    private const float FONT_SIZE_YEAR = 44.0;
    private const float FONT_SIZE_DAY_HEADER = 24.0;
    private const float FONT_SIZE_DAY_CLASSIC = 120.0;
    private const float FONT_SIZE_DAY_MODERN = 90.0;

    public function __construct(
        private readonly string $fontDirectory
    ) {}

    public function render(CalendarGrid $grid, string $outputDirectory, string $template = 'modern'): string
    {
        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0o755, true);
        }

        $image = imagecreatetruecolor(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        if ($image === false) {
            throw new \RuntimeException('Failed to create image.');
        }

        // Enable anti-aliasing
        imageantialias($image, true);

        // Allocate colors
        $bgColor = $this->allocateColor($image, self::COLOR_BG);
        $textColor = $this->allocateColor($image, self::COLOR_TEXT);
        $headerTextColor = $this->allocateColor($image, self::COLOR_HEADER_TEXT);
        $otherMonthColor = $this->allocateColor($image, self::COLOR_OTHER_MONTH);
        $yearColor = $this->allocateColor($image, self::COLOR_YEAR);
        $borderColor = $this->allocateColor($image, self::COLOR_BORDER);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Font paths
        $fontPrefix = ($template === 'classic') ? 'Lora' : 'Inter';
        $fontBold = $this->fontDirectory . '/' . $fontPrefix . '-Bold.ttf';
        $fontRegular = $this->fontDirectory . '/' . $fontPrefix . '-Regular.ttf';
        $fontMedium = $this->fontDirectory . '/' . $fontPrefix . '-Medium.ttf';
        $fontSemiBold = $this->fontDirectory . '/' . $fontPrefix . '-SemiBold.ttf';

        // === Header Section ===
        $headerY = self::PADDING_TOP + 80;
        $contentWidth = self::IMAGE_WIDTH - (2 * self::PADDING_X);

        // Month name (left-aligned, bold, uppercase)
        $monthName = strtoupper($grid->getMonthName());
        imagettftext($image, self::FONT_SIZE_MONTH, 0, self::PADDING_X, $headerY, $textColor, $fontBold, $monthName);

        // Year (right-aligned)
        $yearText = (string) $grid->getYear();
        $yearBox = imagettfbbox(self::FONT_SIZE_YEAR, 0, $fontRegular, $yearText);
        if ($yearBox !== false) {
            $yearWidth = $yearBox[2] - $yearBox[0];
            $yearX = self::IMAGE_WIDTH - self::PADDING_X - $yearWidth;
            imagettftext($image, self::FONT_SIZE_YEAR, 0, $yearX, $headerY, $yearColor, $fontRegular, $yearText);
        }

        // Header border line
        $headerBorderY = $headerY + 30;
        imagesetthickness($image, 4);
        imageline($image, self::PADDING_X, $headerBorderY, self::IMAGE_WIDTH - self::PADDING_X, $headerBorderY, $textColor);

        // === Day Headers ===
        $gridStartY = $headerBorderY + 80;
        $colWidth = $contentWidth / 7;

        $headers = $grid->getHeaders();
        foreach ($headers as $i => $header) {
            $headerUpper = strtoupper($header);
            $box = imagettfbbox(self::FONT_SIZE_DAY_HEADER, 0, $fontSemiBold, $headerUpper);
            if ($box !== false) {
                $textWidth = $box[2] - $box[0];
                $x = (int) (self::PADDING_X + ($i * $colWidth) + ($colWidth - $textWidth) / 2);
                imagettftext($image, self::FONT_SIZE_DAY_HEADER, 0, $x, $gridStartY, $headerTextColor, $fontSemiBold, $headerUpper);
            }
        }

        // Header separator line
        $headerSepY = $gridStartY + 30;

        // === Day Grid ===
        $weeks = $grid->getWeeks();
        $weekCount = count($weeks);
        $gridBottomY = self::IMAGE_HEIGHT - self::PADDING_TOP;
        $availableHeight = $gridBottomY - $headerSepY - 20;
        $rowHeight = $availableHeight / $weekCount;

        if ($template === 'classic') {
            imagesetthickness($image, 3);
            $gridTopY = $gridStartY - 45;
            $gridBottomRealY = (int) ($headerSepY + ($weekCount * $rowHeight));

            // Draw vertical border lines
            for ($i = 0; $i <= 7; $i++) {
                $x = (int) (self::PADDING_X + $i * $colWidth);
                imageline($image, $x, $gridTopY, $x, $gridBottomRealY, $textColor);
            }

            // Draw horizontal border lines
            // 1. Line above headers
            imageline($image, self::PADDING_X, $gridTopY, self::IMAGE_WIDTH - self::PADDING_X, $gridTopY, $textColor);
            // 2. Line below headers
            imageline($image, self::PADDING_X, $headerSepY, self::IMAGE_WIDTH - self::PADDING_X, $headerSepY, $textColor);
            // 3. Lines below each week
            for ($j = 1; $j <= $weekCount; $j++) {
                $y = (int) ($headerSepY + $j * $rowHeight);
                imageline($image, self::PADDING_X, $y, self::IMAGE_WIDTH - self::PADDING_X, $y, $textColor);
            }
        } else {
            // Modern header separator line
            imagesetthickness($image, 2);
            imageline($image, self::PADDING_X, $headerSepY, self::IMAGE_WIDTH - self::PADDING_X, $headerSepY, $borderColor);
        }

        foreach ($weeks as $weekIndex => $week) {
            foreach ($week as $dayIndex => $day) {
                $dayNumber = (string) $day->getDay();
                $color = $day->isCurrentMonth() ? $textColor : $otherMonthColor;
                $font = $day->isCurrentMonth() ? $fontMedium : $fontRegular;

                $dayFontSize = ($template === 'classic') ? self::FONT_SIZE_DAY_CLASSIC : self::FONT_SIZE_DAY_MODERN;
                $box = imagettfbbox($dayFontSize, 0, $font, $dayNumber);
                if ($box !== false) {
                    $textWidth = $box[2] - $box[0];
                    $textHeight = $box[1] - $box[7];
                    $x = (int) (self::PADDING_X + ($dayIndex * $colWidth) + ($colWidth - $textWidth) / 2);
                    if ($template === 'classic') {
                        $y = (int) ($headerSepY + ($weekIndex * $rowHeight) + ($rowHeight + $textHeight) / 2);
                    } else {
                        $y = (int) ($headerSepY + 20 + ($weekIndex * $rowHeight) + ($rowHeight + $textHeight) / 2);
                    }
                    imagettftext($image, $dayFontSize, 0, $x, $y, $color, $font, $dayNumber);
                }
            }
        }

        // Generate filename
        $filename = sprintf(
            'calendar_%d_%d_%d.png',
            $grid->getMonth(),
            $grid->getYear(),
            time()
        );

        $filePath = rtrim($outputDirectory, '/') . '/' . $filename;

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filename;
    }

    /**
     * @param \GdImage $image
     * @param array{0: int, 1: int, 2: int} $rgb
     */
    private function allocateColor(\GdImage $image, array $rgb): int
    {
        $color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        if ($color === false) {
            throw new \RuntimeException(sprintf('Failed to allocate color (%d, %d, %d).', $rgb[0], $rgb[1], $rgb[2]));
        }

        return $color;
    }
}
