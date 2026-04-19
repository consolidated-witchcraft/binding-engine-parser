<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces;

use ConundrumCodex\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;

interface AstNodeInterface
{
    public function getType(): AstNodeTypeEnum;

    public function getSpan(): SourceSpanInterface;

    /**
     * @return list<AstNodeInterface>
     */
    public function getChildren(): array;

    public function hasChildren(): bool;
}
