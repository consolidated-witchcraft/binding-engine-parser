<?php

use ConundrumCodex\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidTextNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\TextNode;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

it(
    'constructs correctly',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidTextNodeException
     */
    function () {
        $span = new SourceSpan(0, 12);
        $text = 'Hello world.';

        $node = new TextNode(
            span: $span,
            text: $text,
        );

        expect($node)->toBeInstanceOf(TextNode::class);
        expect($node->getType())->toBe(AstNodeTypeEnum::Text);
        expect($node->getSpan())->toBe($span);
        expect($node->getChildren())->toBe([]);
        expect($node->hasChildren())->toBeFalse();
        expect($node->getText())->toBe($text);
    }
);

it(
    'rejects empty text',
    function () {
        expect(
            /**
             * @throws SourceSpanConstructionException
             * @throws InvalidTextNodeException
             */
            function () {
                new TextNode(
                    span: new SourceSpan(0, 0),
                    text: '',
                );
            }
        )->toThrow(InvalidTextNodeException::class);
    }
);
