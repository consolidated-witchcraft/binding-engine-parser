<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;

interface BindingPayloadInterface
{
    public function getSpan(): SourceSpanInterface;

    public function getRaw(): string;
}
