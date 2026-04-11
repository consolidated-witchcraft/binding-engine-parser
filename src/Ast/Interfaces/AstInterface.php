<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Interfaces;

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\AstNodeInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\DocumentNodeInterface;

interface AstInterface
{
    public function getRoot(): DocumentNodeInterface;

    /**
     * @return iterable<AstNodeInterface>
     */
    public function walk(): iterable;
}