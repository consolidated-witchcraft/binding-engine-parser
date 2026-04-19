<?php

declare(strict_types=1);

use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\AttributeAssignmentNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidAttributeAssignmentNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

\it(
    'constructs correctly with valid identifiers',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidAttributeAssignmentNodeException
     */
    function ($validIdentifier) {
        $sourceSpan = new SourceSpan(
            start: 0,
            end: 10
        );

        $value = 'test-value';
        $raw = 'test-value';
        $attributeAssignmentNode = new AttributeAssignmentNode(
            span: $sourceSpan,
            identifier: $validIdentifier,
            value: $value,
            raw: $raw
        );

        \expect($attributeAssignmentNode->getSpan())->toBe($sourceSpan);
        \expect($attributeAssignmentNode->getIdentifier())->toBe($validIdentifier);
        \expect($attributeAssignmentNode->getValue())->toBe($value);
        \expect($attributeAssignmentNode->getRaw())->toBe($raw);
    }
)->with(
    function (): iterable {
        yield 'minimum letters only' => 'abc';
        yield 'minimum with digit' => 'a1b';
        yield 'minimum with underscore' => 'a_b';
        yield 'minimum with hyphen' => 'a-b';
        yield 'internal underscore' => 'test_identifier';
        yield 'internal hyphen' => 'test-identifier';
        yield 'mixed internal separators' => 'test_identifier-1';
        yield 'maximum length' => 'a' . str_repeat('b', 63);
    }
);

\it(
    'rejects invalid identifiers',
    function (string $invalidIdentifier) {
        \expect(
            /**
             * @throws InvalidAttributeAssignmentNodeException
             * @throws SourceSpanConstructionException
             */
            function () use ($invalidIdentifier) {
                new AttributeAssignmentNode(
                    span: new SourceSpan(0, 10),
                    identifier: $invalidIdentifier,
                    value: 'test',
                    raw: 'test'
                );
            }
        )->toThrow(InvalidAttributeAssignmentNodeException::class);
    }
)->with(function (): iterable {
    yield 'too short' => 'a';
    yield 'too short two characters' => 'ab';
    yield 'too long' => str_repeat('b', 65);
    yield 'starts with a digit' => '1test';
    yield 'starts with an underscore' => '_test';
    yield 'starts with a hyphen' => '-test';
    yield 'contains spaces' => 'test identifier';
    yield 'contains punctuation' => 'test.identifier';
    yield 'contains backslash' => 'test\identifier';
    yield 'contains forward slash' => 'test/identifier';
    yield 'contains uppercase' => 'Test-Identifier';
    yield 'contains non-ascii' => 'über';
    yield 'empty' => '';
    yield 'whitespace only' => '  ';
    yield 'consecutive underscore separators' => 'test__identifier';
    yield 'consecutive hyphen separators' => 'test--identifier';
    yield 'underscores after hyphens' => 'test-_identifier';
    yield 'hyphens after underscores' => 'test_-identifier';
    yield 'trailing hyphen' => 'test-identifier-';
    yield 'trailing underscore' => 'test-identifier_';
});

\it(
    'rejects invalid values',
    function (string $invalidValue) {
        \expect(
            /**
             * @throws InvalidAttributeAssignmentNodeException
             * @throws SourceSpanConstructionException
             */
            function () use ($invalidValue) {
                new AttributeAssignmentNode(
                    span: new SourceSpan(0, 10),
                    identifier: 'test-identifier',
                    value: $invalidValue,
                    raw: 'test'
                );
            }
        )->toThrow(InvalidAttributeAssignmentNodeException::class);
    }
)->with(
    function (): iterable {
        yield 'empty' => '';
        yield 'whitespace only' => '  ';
    }
);

\it(
    'rejects invalid raws',
    function (string $invalidRaw) {
        \expect(
            /**
             * @throws InvalidAttributeAssignmentNodeException
             * @throws SourceSpanConstructionException
             */
            function () use ($invalidRaw) {
                new AttributeAssignmentNode(
                    span: new SourceSpan(0, 10),
                    identifier: 'test-identifier',
                    value: 'value',
                    raw: $invalidRaw
                );
            }
        )->toThrow(InvalidAttributeAssignmentNodeException::class);
    }
)->with(
    function (): iterable {
        yield 'empty' => '';
        yield 'whitespace only' => '  ';
    }
);
