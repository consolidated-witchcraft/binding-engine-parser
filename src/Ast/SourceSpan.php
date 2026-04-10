<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast;

use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;

final readonly class SourceSpan implements SourceSpanInterface
{
    /**
     * @param int $start
     * @param int $end
     * @throws SourceSpanConstructionException
     */
    public function __construct(
        private int $start,
        private int $end,
    ) {
        if ($start < 0) {
            throw new SourceSpanConstructionException('SourceSpan start must be >= 0.');
        }

        if ($end < $start) {
            throw new SourceSpanConstructionException('SourceSpan end must be >= start.');
        }
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }

    public function length(): int
    {
        return $this->end - $this->start;
    }

    public function contains(int $offset): bool
    {
        return $offset >= $this->start && $offset < $this->end;
    }

    public function containsSpan(SourceSpanInterface $comparisonSpan): bool
    {
        return $comparisonSpan->start() >= $this->start
            && $comparisonSpan->end() <= $this->end;
    }

    public function overlapsSpan(SourceSpanInterface $comparisonSpan): bool
    {
        if ($this->length() === 0 || $comparisonSpan->length() === 0) {
            return false;
        }

        return $this->start < $comparisonSpan->end()
            && $this->end > $comparisonSpan->start();
    }

    public function equalsSpan(SourceSpanInterface $comparisonSpan): bool
    {
        return $this->start === $comparisonSpan->start()
            && $this->end === $comparisonSpan->end();
    }

    public function extract(string $source): string
    {
        return substr($source, $this->start, $this->length());
    }

    public function __toString(): string
    {
        return sprintf('[%d, %d)', $this->start, $this->end);
    }
}
