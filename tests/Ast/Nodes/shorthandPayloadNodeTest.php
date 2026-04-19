<?php

use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidShorthandPayloadNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\ShorthandPayloadNode;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

\it(
    'constructs correctly',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidShorthandPayloadNodeException
     */
    function () {
        $sourceSpan = new SourceSpan(0, 50);
        $value = 'jane-austen';
        $raw = 'jane-austen';
        $shorthandPayloadNode = new ShorthandPayloadNode(
            span: $sourceSpan,
            value: $value,
            raw: $raw
        );
        \expect($shorthandPayloadNode->getValue())->toBe($value);
        \expect($shorthandPayloadNode->getRaw())->toBe($raw);
        \expect($shorthandPayloadNode->getSpan())->toBe($sourceSpan);
    }
);

\it(
    'rejects invalid values',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidShorthandPayloadNodeException
     */
    function (string $invalidValue) {

        \expect(function () use ($invalidValue) {

            new ShorthandPayloadNode(
                span: new SourceSpan(0, 50),
                value: $invalidValue,
                raw: 'jane-austen'
            );
        })->toThrow(InvalidShorthandPayloadNodeException::class);
    }
)->with(function (): iterable {
    yield 'empty_string' => '';
    yield 'all_whitespace' => '    ';
});

\it(
    'rejects invalid raws',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidShorthandPayloadNodeException
     */
    function (string $invalidRaw) {

        \expect(function () use ($invalidRaw) {
            new ShorthandPayloadNode(
                span: new SourceSpan(0, 50),
                value: 'jane-austen',
                raw: $invalidRaw
            );
        })->toThrow(InvalidShorthandPayloadNodeException::class);
    }
)->with(function (): iterable {
    yield 'empty_string' => '';
    yield 'all_whitespace' => '    ';
});
