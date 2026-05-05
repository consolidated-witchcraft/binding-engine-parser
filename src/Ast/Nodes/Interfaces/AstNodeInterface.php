<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;

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
