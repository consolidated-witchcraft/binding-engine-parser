<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Interfaces\AstNodeInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Interfaces\DocumentNodeInterface;

interface AstInterface
{
    public function getRoot(): DocumentNodeInterface;

    /**
     * @return iterable<AstNodeInterface>
     */
    public function walk(): iterable;
}
