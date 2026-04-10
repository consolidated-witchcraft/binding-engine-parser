<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces;

use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;

interface BindingPayloadInterface
{
    public function getSpan(): SourceSpanInterface;

    public function getRaw(): string;
}
