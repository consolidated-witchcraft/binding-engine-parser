<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes;

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidAttributeAssignmentNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

readonly class AttributeAssignmentNode
{
    private const string VALID_IDENTIFIER_PATTERN = '/^[a-z](?:[a-z0-9]|[_-](?=[a-z0-9])){2,63}$/';

    /**
     * @throws InvalidAttributeAssignmentNodeException
     */
    public function __construct(
        private SourceSpan $span,
        private string $identifier,
        private string $value,
        private string $raw,
    ) {
        $this->guard();
    }

    public function getSpan(): SourceSpan
    {
        return $this->span;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * @throws InvalidAttributeAssignmentNodeException
     */
    private function guard(): void
    {
        if (trim($this->identifier) === '') {
            throw new InvalidAttributeAssignmentNodeException(
                message: 'Attribute assignment identifier must not be empty.',
                sourceSpan: $this->span,
            );
        }

        if (trim($this->value) === '') {
            throw new InvalidAttributeAssignmentNodeException(
                message: 'Attribute assignment value must not be empty.',
                sourceSpan: $this->span,
            );
        }

        if (trim($this->raw) === '') {
            throw new InvalidAttributeAssignmentNodeException(
                message: 'Attribute assignment raw source must not be empty.',
                sourceSpan: $this->span,
            );
        }

        if (!preg_match(self::VALID_IDENTIFIER_PATTERN, $this->identifier)) {
            throw new InvalidAttributeAssignmentNodeException(
                message: sprintf(
                    'Attribute assignment identifier "%s" is invalid.',
                    $this->identifier,
                ),
                sourceSpan: $this->span,
            );
        }
    }
}