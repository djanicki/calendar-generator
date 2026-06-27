<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\CalendarGrid;

interface CalendarImageRendererInterface
{
    /**
     * Renders the calendar grid as a PNG image file.
     *
     * @param CalendarGrid $grid            The calendar grid data to render.
     * @param string       $outputDirectory Absolute path to the output directory.
     * @param string       $template        The template style to use.
     *
     * @return string The filename (not full path) of the generated image.
     */
    public function render(CalendarGrid $grid, string $outputDirectory, string $template = 'modern'): string;
}
