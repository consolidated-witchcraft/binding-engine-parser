<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Interfaces;

interface AstInterface
{
    public function root(): DocumentNodeInterface;

    /**
     * @return iterable<AstNodeInterface>
     */
    public function walk(): iterable;
}