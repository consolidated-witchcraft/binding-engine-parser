<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\AttributeAssignmentNode;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\AttributeListPayloadNode;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidAttributeAssignmentNodeException;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidAttributeListPayloadNodeException;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\SourceSpan;

\it(
    'constructs correctly (happy path)',
    /**
     * @throws SourceSpanConstructionException
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     */
    function () {
        $sourceSpan = new SourceSpan(0, 50);

        $attributes = [
            new AttributeAssignmentNode(
                $sourceSpan,
                'test-identifier-1',
                'test raw',
                'test raw'
            ),
            new AttributeAssignmentNode(
                $sourceSpan,
                'test-identifier-2',
                'test raw',
                'test raw'
            )
        ];

        $raw = 'Test Raw';
        $attributeListPayload = new AttributeListPayloadNode(
            span: $sourceSpan,
            attributes: $attributes,
            raw: $raw,
        );
        \expect($attributeListPayload->getSpan())->toBe($sourceSpan);
        \expect($attributeListPayload->getAttributes())->toBe($attributes);
        \expect($attributeListPayload->getRaw())->toBe($raw);
    }
);

\it('rejects empty attributes', function () {

    \expect(
        /**
         * @throws SourceSpanConstructionException
         * @throws InvalidAttributeListPayloadNodeException
         */
        function () {
            $sourceSpan = new SourceSpan(0, 50);
            new AttributeListPayloadNode(
                span: $sourceSpan,
                attributes: [],
                raw: 'test raw',
            );
        }
    )->toThrow(InvalidAttributeListPayloadNodeException::class);

});

\it('rejects empty or whitespace-only raw', function (string $invalidRaw) {
    \expect(
        /**
         * @throws SourceSpanConstructionException
         * @throws InvalidAttributeListPayloadNodeException
         * @throws InvalidAttributeAssignmentNodeException
         */
        function () use ($invalidRaw) {
            $sourceSpan = new SourceSpan(0, 50);
            $attributes = [
                new AttributeAssignmentNode(
                    $sourceSpan,
                    'test-identifier',
                    'test raw',
                    'test raw'
                )
            ];
            new AttributeListPayloadNode(
                span: $sourceSpan,
                attributes: $attributes,
                raw: $invalidRaw,
            );
        }
    )->toThrow(InvalidAttributeListPayloadNodeException::class);
})->with(function (): iterable {
    yield 'empty string' => '';
    yield 'oops, all whitespace' => '     ';
});

\it(
    'correctly identifies attributes it possesses',
    /**
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     * @throws SourceSpanConstructionException
     */
    function () {
        $sourceSpan = new SourceSpan(0, 50);

        $attributes = [
            new AttributeAssignmentNode(
                $sourceSpan,
                'test-identifier-1',
                'test raw',
                'test raw'
            ),
            new AttributeAssignmentNode(
                $sourceSpan,
                'test-identifier-2',
                'test raw',
                'test raw'
            )
        ];

        $attributeListPayload = new AttributeListPayloadNode(
            span: $sourceSpan,
            attributes: $attributes,
            raw: 'test raw',
        );
        \expect($attributeListPayload->hasAttribute('test-identifier-1'))->toBeTrue();
        \expect($attributeListPayload->hasAttribute('test-identifier-2'))->toBeTrue();
        \expect($attributeListPayload->hasAttribute('test-identifier-3'))->toBeFalse();
    }
);

\it(
    'can correctly retrieve attributes it possesses',
    /**
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     * @throws SourceSpanConstructionException
     */
    function () {
        $sourceSpan = new SourceSpan(0, 50);

        $attributeOne  = new AttributeAssignmentNode(
            $sourceSpan,
            'test-identifier-1',
            'test raw',
            'test raw'
        );
        $attributeTwo = new AttributeAssignmentNode(
            $sourceSpan,
            'test-identifier-2',
            'test raw',
            'test raw'
        );
        $attributes = [
            $attributeOne,
            $attributeTwo,
        ];

        $attributeListPayload = new AttributeListPayloadNode(
            span: $sourceSpan,
            attributes: $attributes,
            raw: 'test raw',
        );

        \expect($attributeListPayload->getAttribute('test-identifier-1'))->toBe($attributeOne);
        \expect($attributeListPayload->getAttribute('test-identifier-2'))->toBe($attributeTwo);
        \expect($attributeListPayload->getAttribute('test-identifier-3'))->toBeNull();
    }
);

it(
    'allows duplicate attribute identifiers at parser level',

    /**
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     * @throws SourceSpanConstructionException
     */
    function () {
        $sourceSpan = new SourceSpan(0, 50);

        $attributes = [
            new AttributeAssignmentNode($sourceSpan, 'type', 'birth', 'type=birth'),
            new AttributeAssignmentNode($sourceSpan, 'type', 'death', 'type=death'),
        ];

        $payload = new AttributeListPayloadNode(
            span: $sourceSpan,
            attributes: $attributes,
            raw: 'type=birth; type=death',
        );

        expect($payload->getAttributes())->toHaveCount(2)
            ->and($payload->hasAttribute('type'))->toBeTrue()
            ->and($payload->getAttribute('type'))->toBe($attributes[0]);
    }
);
