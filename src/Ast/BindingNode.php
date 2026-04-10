<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast;

use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\AstNodeInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
final readonly class BindingNode implements AstNodeInterface
{
    /**
     * @param array<string, string> $payload
     */
    public function __construct(
        private SourceSpanInterface $span,
        private string $bindingType,
        private array $payload,
        private string $label,
    ) {
    }

    public function type(): AstNodeTypeEnum
    {
        return AstNodeTypeEnum::Binding;
    }

    public function span(): SourceSpanInterface
    {
        return $this->span;
    }

    public function bindingType(): string
    {
        return $this->bindingType;
    }

    /**
     * @return array<string, string>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    public function hasPayloadAttribute(string $key): bool
    {
        return array_key_exists($key, $this->payload);
    }

    public function payloadAttribute(string $key): ?string
    {
        return $this->payload[$key] ?? null;
    }

    public function label(): string
    {
        return $this->label;
    }

    /**
     * @return list<AstNodeInterface>
     */
    public function children(): array
    {
        return [];
    }

    public function hasChildren(): bool
    {
        return false;
    }
}
