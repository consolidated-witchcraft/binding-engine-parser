<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces;

interface DocumentNodeInterface extends AstNodeInterface
{
    /**
     * @return list<AstNodeInterface>
     */
    public function getChildren(): array;
}
