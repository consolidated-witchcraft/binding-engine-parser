<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Interfaces\AstNodeInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\SourceSpan;

readonly class DocumentNode implements AstNodeInterface
{
    /**
     * @param list<AstNodeInterface> $children
     */
    public function __construct(
        private SourceSpan $span,
        private array $children,
    ) {
    }

    public function getType(): AstNodeTypeEnum
    {
        return AstNodeTypeEnum::Document;
    }

    public function getSpan(): SourceSpanInterface
    {
        return $this->span;
    }

    /**
     * @return list<AstNodeInterface>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }
}
