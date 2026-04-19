<?php

use ConundrumCodex\BindingEngine\Parser\Ast\Enums\AstNodeTypeEnum;
use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\BindingNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidBindingNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidShorthandPayloadNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\ShorthandPayloadNode;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

\it(
    'constructs correctly',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidShorthandPayloadNodeException
     * @throws InvalidBindingNodeException
     */
    function () {
        $sourceSpan = new SourceSpan(0, 50);
        $bindingType = 'test-type';
        $payload = new ShorthandPayloadNode($sourceSpan, 'value', 'value');
        $raw = 'test raw value';
        $label = 'Test Label';

        $bindingNode = new BindingNode(
            span: $sourceSpan,
            bindingType: $bindingType,
            payload: $payload,
            raw: $raw,
            label: $label
        );
        \expect($bindingNode)->toBeInstanceOf(BindingNode::class);
        \expect($bindingNode->getRaw())->toBe($raw);
        \expect($bindingNode->getLabel())->toBe($label);
        \expect($bindingNode->getBindingType())->toBe($bindingType);
        \expect($bindingNode->getPayload())->toBe($payload);
        \expect($bindingNode->getSpan())->toBe($sourceSpan);
        \expect($bindingNode->hasLabel())->toBeTrue();
        \expect($bindingNode->hasChildren())->toBeFalse();
        \expect($bindingNode->getChildren())->toBe([]);
        \expect($bindingNode->getType())->toBe(AstNodeTypeEnum::Binding);
    }
);

\it(
    'constructs correctly without a label',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidShorthandPayloadNodeException
     * @throws InvalidBindingNodeException
     */
    function () {
        $sourceSpan = new SourceSpan(0, 50);
        $bindingType = 'test-type';
        $payload = new ShorthandPayloadNode($sourceSpan, 'value', 'value');
        $raw = 'test raw value';

        $bindingNode = new BindingNode(
            span: $sourceSpan,
            bindingType: $bindingType,
            payload: $payload,
            raw: $raw,
        );

        \expect($bindingNode)->toBeInstanceOf(BindingNode::class);
        \expect($bindingNode->getRaw())->toBe($raw);
        \expect($bindingNode->getLabel())->toBeNull();
        \expect($bindingNode->getBindingType())->toBe($bindingType);
        \expect($bindingNode->getPayload())->toBe($payload);
        \expect($bindingNode->getSpan())->toBe($sourceSpan);
        \expect($bindingNode->hasLabel())->toBeFalse();
    }
);

\it(
    'rejects invalid binding types',
    function (string $invalidBindingType) {
        \expect(
            /**
             * @throws InvalidBindingNodeException
             * @throws InvalidShorthandPayloadNodeException
             * @throws SourceSpanConstructionException
             */
            function () use ($invalidBindingType) {
                $sourceSpan = new SourceSpan(0, 50);

                new BindingNode(
                    span: $sourceSpan,
                    bindingType: $invalidBindingType,
                    payload: new ShorthandPayloadNode($sourceSpan, 'value', 'value'),
                    raw: 'test raw value',
                );
            }
        )->toThrow(InvalidBindingNodeException::class);
    }
)->with(function (): iterable {
    yield 'contains underscores' => 'event_type';
    yield 'contains consecutive separators' => 'event--type';
    yield 'contains trailing separators' => 'event-';
    yield 'contains leading separators' => '-event';
    yield 'contains uppercase letters' => 'eventType';
    yield 'contains spaces' => 'event type';
    yield 'contains invalid characters' => 'event-#type';
    yield 'too short one character' => 'a';
    yield 'too short two characters' => 'ab';
    yield 'too long' => str_repeat('a', 65);
    yield 'starts with digit' => '1event';
    yield 'empty string' => '';
    yield 'all whitespace' => '   ';
});

\it(
    'rejects invalid labels',
    function (string $invalidLabel) {
        \expect(
            /**
             * @throws InvalidBindingNodeException
             * @throws InvalidShorthandPayloadNodeException
             * @throws SourceSpanConstructionException
             */
            function () use ($invalidLabel) {
                $sourceSpan = new SourceSpan(0, 50);

                new BindingNode(
                    span: $sourceSpan,
                    bindingType: 'binding-type',
                    payload: new ShorthandPayloadNode($sourceSpan, 'value', 'value'),
                    raw: 'test raw value',
                    label: $invalidLabel
                );
            }
        )->toThrow(InvalidBindingNodeException::class);
    }
)->with(function (): iterable {
    yield 'empty string' => '';
    yield 'all whitespace' => '   ';
});

\it(
    'rejects invalid raw values',
    function (string $invalidRawValue) {
        \expect(
            /**
             * @throws InvalidBindingNodeException
             * @throws InvalidShorthandPayloadNodeException
             * @throws SourceSpanConstructionException
             */
            function () use ($invalidRawValue) {
                $sourceSpan = new SourceSpan(0, 50);

                new BindingNode(
                    span: $sourceSpan,
                    bindingType: 'binding-type',
                    payload: new ShorthandPayloadNode($sourceSpan, 'value', 'value'),
                    raw: $invalidRawValue
                );
            }
        )->toThrow(InvalidBindingNodeException::class);
    }
)->with(function (): iterable {
    yield 'empty string' => '';
    yield 'all whitespace' => '   ';
});
