<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Interfaces;

interface DocumentNodeInterface extends AstNodeInterface
{
    /**
     * @return list<AstNodeInterface>
     */
    public function children(): array;
}
