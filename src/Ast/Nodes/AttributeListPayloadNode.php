<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes;

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidAttributeListPayloadNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\BindingPayloadInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;
//TODO - THIS NEEDS A TEST!
readonly class AttributeListPayloadNode implements BindingPayloadInterface
{
    /**
     * @param AttributeAssignmentNode[] $attributes
     * @throws InvalidAttributeListPayloadNodeException
     */
    public function __construct(
        private SourceSpan $span,
        private array $attributes,
        private string $raw,
    ) {
        $this->guard();
    }

    public function getSpan(): SourceSpan
    {
        return $this->span;
    }

    /**
     * @return AttributeAssignmentNode[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function hasAttribute(string $identifier): bool
    {
        return $this->getAttribute($identifier) !== null;
    }

    public function getAttribute(string $identifier): ?AttributeAssignmentNode
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getIdentifier() === $identifier) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @throws InvalidAttributeListPayloadNodeException
     */
    private function guard(): void
    {
        if ($this->attributes === []) {
            throw new InvalidAttributeListPayloadNodeException(
                message: 'Attribute list payload must contain at least one attribute assignment.',
                sourceSpan: $this->span,
            );
        }

        if (trim($this->raw) === '') {
            throw new InvalidAttributeListPayloadNodeException(
                message: 'Attribute list payload raw source must not be empty.',
                sourceSpan: $this->span,
            );
        }
    }
}