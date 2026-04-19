<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes;

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidShorthandPayloadNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\BindingPayloadInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

readonly class ShorthandPayloadNode implements BindingPayloadInterface
{
    /**
     * @throws InvalidShorthandPayloadNodeException
     */
    public function __construct(
        private SourceSpan $span,
        private string $value,
        private string $raw,
    ) {
        $this->guard();
    }

    public function getSpan(): SourceSpan
    {
        return $this->span;
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
     * @throws InvalidShorthandPayloadNodeException
     */
    private function guard(): void
    {
        if (trim($this->value) === '') {
            throw new InvalidShorthandPayloadNodeException(
                message: 'Shorthand payload value must not be empty.',
                sourceSpan: $this->span
            );
        }

        if (trim($this->raw) === '') {
            throw new InvalidShorthandPayloadNodeException(
                message: 'Shorthand payload raw source must not be empty.',
                sourceSpan: $this->span
            );
        }
    }
}
