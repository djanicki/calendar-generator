<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class Calendar
{
    public function __construct(
        private readonly string $token,
        private readonly int $selMonth,
        private readonly int $selYear,
        private readonly bool $mondayFirst,
        private readonly string $generatedFile,
        private readonly \DateTimeImmutable $expiresAt,
        private readonly \DateTimeImmutable $createdAt,
        private readonly ?int $id = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getSelMonth(): int
    {
        return $this->selMonth;
    }

    public function getSelYear(): int
    {
        return $this->selYear;
    }

    public function isMondayFirst(): bool
    {
        return $this->mondayFirst;
    }

    public function getGeneratedFile(): string
    {
        return $this->generatedFile;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }
}
