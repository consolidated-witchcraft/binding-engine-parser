<?php

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\DocumentNode;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidTextNodeException;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\TextNode;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\SourceSpan;

it(
    'constructs correctly without children',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $span = new SourceSpan(0, 0);

        $node = new DocumentNode(
            span: $span,
            children: [],
        );

        expect($node)->toBeInstanceOf(DocumentNode::class)
            ->and($node->getType())->toBe(AstNodeTypeEnum::Document)
            ->and($node->getSpan())->toBe($span)
            ->and($node->getChildren())->toBe([])
            ->and($node->hasChildren())->toBeFalse();
    }
);

it(
    'constructs correctly with children',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidTextNodeException
     */
    function () {
        $span = new SourceSpan(0, 12);

        $child = new TextNode(
            span: $span,
            text: 'Hello world.',
        );

        $children = [$child];

        $node = new DocumentNode(
            span: $span,
            children: $children,
        );

        expect($node)->toBeInstanceOf(DocumentNode::class)
            ->and($node->getType())->toBe(AstNodeTypeEnum::Document)
            ->and($node->getSpan())->toBe($span)
            ->and($node->getChildren())->toBe($children)
            ->and($node->hasChildren())->toBeTrue();
    }
);
