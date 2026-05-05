<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Interfaces;

interface DocumentNodeInterface extends AstNodeInterface
{
    /**
     * @return list<AstNodeInterface>
     */
    public function getChildren(): array;
}
