<?php

namespace App\DTO;

use Random\RandomException;

class DiceDataDTO
{
    private function __construct(
        /** @var array<int> $original */
        private readonly array $original,
        /** @var array<int> $result */
        private array $result,
        private bool $isIndividual = false,
        private ?int $successCount = null,
        private int $modifier = 0,
    ) {}

    /**
     * @throws RandomException
     */
    public static function make(int $counts, int $sides): self
    {
        $results = [];

        for ($i = 0; $i < $counts; $i++) {
            $results[] = random_int(1, $sides);
        }

        $original = $results;
        return new self(
            $original,
            $results,
        );
    }

    public function getOriginal(): array
    {
        return $this->original;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function modifyResultBy(int $value): void
    {
        array_walk($this->result, function (&$item) use ($value) {
            $item += $value;
        });

        $this->modifier = $value;
    }

    public function isIndividual(): bool
    {
        return $this->isIndividual;
    }

    public function setIndividual(bool $isIndividual): void
    {
        $this->isIndividual = $isIndividual;
    }

    public function getSuccessCount(): ?int
    {
        return $this->successCount;
    }

    public function setSuccessCount(?int $successCount): void
    {
        $this->successCount = $successCount;
    }

    public function appendSuccess(): void
    {
        if ($this->successCount === null || !$this->isIndividual) {
            $this->successCount = 1;
            return;
        }

        $this->successCount++;
    }

    public function setSuccess(): void
    {
        $this->successCount = 0;
    }

    private function getTotal(): ?int
    {
        $result = 0;
        foreach ($this->result as $item) {
            $result += $item;
        }

        return $result;
    }

    public function toString(): string
    {
        $str = $this->isIndividual ?
            implode(',', $this->original)
            : implode('+', $this->original);

        if ($this->modifier != 0) {
            $str .= ($this->modifier > 0 ? ' + ' : ' - ') . abs($this->modifier);
        }

        $str .= ' = ';
        $str .= $this->isIndividual ? implode(', ', $this->result) : $this->getTotal();
        $str .= $this->successCount !== null ? " ($this->successCount Successes)" : "";

        return "<b>" . $str . "</b>";
    }
}
