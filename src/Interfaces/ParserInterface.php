<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Interfaces;

use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\AstInterface;

interface ParserInterface
{
    public function parse(string $source): AstInterface;
}
