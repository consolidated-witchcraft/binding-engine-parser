<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Enums;

enum AstNodeTypeEnum: string
{
    case Document = 'document';
    case Paragraph = 'paragraph';
    case Text = 'text';
    case Binding = 'binding';

    public function isContainer(): bool
    {
        return match ($this) {
            self::Document,
            self::Paragraph => true,
            default => false,
        };
    }

    public function isLeaf(): bool
    {
        return !$this->isContainer();
    }
}
