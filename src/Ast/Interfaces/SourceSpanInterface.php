<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Interfaces;

interface SourceSpanInterface extends \Stringable
{
    public function start(): int;

    public function end(): int;

    public function length(): int;

    public function contains(int $offset): bool;

    public function containsSpan(SourceSpanInterface $comparisonSpan): bool;

    public function overlapsSpan(SourceSpanInterface $comparisonSpan): bool;

    public function equalsSpan(SourceSpanInterface $comparisonSpan): bool;

    public function extract(string $source): string;
}
