<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces;

interface SourceSpanInterface extends \Stringable
{
    public function getStart(): int;

    public function getEnd(): int;

    public function getLength(): int;

    public function contains(int $offset): bool;

    public function containsSpan(SourceSpanInterface $comparisonSpan): bool;

    public function overlapsSpan(SourceSpanInterface $comparisonSpan): bool;

    public function equalsSpan(SourceSpanInterface $comparisonSpan): bool;

    public function extract(string $source): string;
}
