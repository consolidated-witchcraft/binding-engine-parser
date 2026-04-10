<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Nodes;

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\AstNodeInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\BindingPayloadInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

final readonly class BindingNode implements AstNodeInterface
{
    public function __construct(
        private SourceSpan $span,
        private string $bindingType,
        private BindingPayloadInterface $payload,
        private ?string $label,
        private string $raw,
    ) {
        $this->guard();
    }

    public function getSpan(): SourceSpan
    {
        return $this->span;
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

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function hasLabel(): bool
    {
        return $this->label !== null;
    }

    private function guard(): void
    {
        if (trim($this->bindingType) === '') {
            throw new \InvalidArgumentException('Binding type must not be empty.');
        }

        if (trim($this->raw) === '') {
            throw new \InvalidArgumentException('Binding raw source must not be empty.');
        }

        if ($this->label !== null && trim($this->label) === '') {
            throw new \InvalidArgumentException('Binding label must not be empty when provided.');
        }
    }
}