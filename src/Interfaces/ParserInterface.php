<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\AstInterface;

interface ParserInterface
{
    public function parse(string $source): AstInterface;
}
