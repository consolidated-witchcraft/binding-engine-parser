<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Interfaces;

use ConundrumCodex\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;

interface AstNodeInterface
{
    public function type(): AstNodeTypeEnum;

    public function span(): SourceSpanInterface;

    /**
     * @return list<AstNodeInterface>
     */
    public function children(): array;

    public function hasChildren(): bool;
}