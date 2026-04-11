<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes;

use ConundrumCodex\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidTextNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\AstNodeInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

readonly class TextNode implements AstNodeInterface
{
    /**
     * @throws InvalidTextNodeException
     */
    public function __construct(
        private SourceSpan $span,
        private string $text,
    ) {
        $this->guard();
    }

    public function getType(): AstNodeTypeEnum
    {
        return AstNodeTypeEnum::Text;
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
        return [];
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @throws InvalidTextNodeException
     */
    private function guard(): void
    {
        if ($this->text === '') {
            throw new InvalidTextNodeException(
                message: 'Text node text must not be empty.',
                sourceSpan: $this->span,
            );
        }
    }
}