<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes;

use ConundrumCodex\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidBindingNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\AstNodeInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\BindingPayloadInterface;
use ConundrumCodex\BindingEngine\Parser\Language\IdentifierPatterns;

readonly class BindingNode implements AstNodeInterface
{
    /**
     * @throws InvalidBindingNodeException
     */
    public function __construct(
        private SourceSpanInterface $span,
        private string $bindingType,
        private BindingPayloadInterface $payload,
        private string $raw,
        private ?string $label = null,
    ) {
        $this->guard();
    }

    public function getType(): AstNodeTypeEnum
    {
        return AstNodeTypeEnum::Binding;
    }

    public function getSpan(): SourceSpanInterface
    {
        return $this->span;
    }

    /**
     * @return AstNodeInterface[]
     */
    public function getChildren(): array
    {
        return [];
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function getBindingType(): string
    {
        return $this->bindingType;
    }

    public function getPayload(): BindingPayloadInterface
    {
        return $this->payload;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function hasLabel(): bool
    {
        return $this->label !== null;
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * @throws InvalidBindingNodeException
     */
    private function guard(): void
    {
        if (!preg_match(IdentifierPatterns::BINDING_TYPE, $this->bindingType)) {
            throw new InvalidBindingNodeException(
                message: sprintf(
                    'Binding type "%s" is invalid.',
                    $this->bindingType,
                ),
                sourceSpan: $this->span,
            );
        }

        if ($this->label !== null && trim($this->label) === '') {
            throw new InvalidBindingNodeException(
                message: 'Binding label must not be empty when provided.',
                sourceSpan: $this->span,
            );
        }

        if (trim($this->raw) === '') {
            throw new InvalidBindingNodeException(
                message: 'Binding raw source must not be empty.',
                sourceSpan: $this->span,
            );
        }
    }
}
