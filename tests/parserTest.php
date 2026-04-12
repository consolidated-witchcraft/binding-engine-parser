<?php

declare(strict_types=1);

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\AttributeListPayloadNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\BindingNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\TextNode;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConundrumCodex\BindingEngine\Parser\Parser;

it(
    'parses quoted attribute values with spaces',
    function () {
        $parser = new Parser();

        $source = '@event[title:"The Fall of Gondolin"]';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(BindingNode::class);

        $payload = $children[0]->getPayload();

        expect($payload)->toBeInstanceOf(AttributeListPayloadNode::class)
            ->and($payload->getAttributes())->toHaveCount(1)
            ->and($payload->getAttributes()[0]->getIdentifier())->toBe('title')
            ->and($payload->getAttributes()[0]->getValue())->toBe('The Fall of Gondolin');
    }
);

it(
    'allows commas inside quoted attribute values',
    function () {
        $parser = new Parser();

        $source = '@event[note:"a, b, c", type:birth]';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(BindingNode::class);

        $payload = $children[0]->getPayload();

        expect($payload)->toBeInstanceOf(AttributeListPayloadNode::class)
            ->and($payload->getAttributes())->toHaveCount(2)
            ->and($payload->getAttributes()[0]->getIdentifier())->toBe('note')
            ->and($payload->getAttributes()[0]->getValue())->toBe('a, b, c')
            ->and($payload->getAttributes()[1]->getIdentifier())->toBe('type')
            ->and($payload->getAttributes()[1]->getValue())->toBe('birth');
    }
);

it(
    'allows colons inside quoted attribute values',
    function () {
        $parser = new Parser();

        $source = '@event[note:"before: after", type:birth]';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(BindingNode::class);

        $payload = $children[0]->getPayload();

        expect($payload)->toBeInstanceOf(AttributeListPayloadNode::class)
            ->and($payload->getAttributes())->toHaveCount(2)
            ->and($payload->getAttributes()[0]->getIdentifier())->toBe('note')
            ->and($payload->getAttributes()[0]->getValue())->toBe('before: after')
            ->and($payload->getAttributes()[1]->getIdentifier())->toBe('type')
            ->and($payload->getAttributes()[1]->getValue())->toBe('birth');
    }
);

it(
    'creates a diagnostic for an unterminated quoted attribute value',
    function () {
        $parser = new Parser();

        $source = '@event[title:"Unclosed, type:birth]';
        $result = $parser->parse($source);

        expect($result->hasDiagnostics())->toBeTrue()
            ->and($result->hasErrors())->toBeTrue()
            ->and($result->getDiagnostics())->toHaveCount(1);

        $diagnostic = $result->getDiagnostics()[0];

        expect($diagnostic->getSeverity())->toBe(DiagnosticSeverityEnum::Error)
            ->and($diagnostic->getCode())->toBe('parser.attribute_assignment.malformed')
            ->and($diagnostic->getSourceSpan())->not->toBeNull();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(TextNode::class)
            ->and($children[0]->getText())->toBe($source);
    }
);

it(
    'continues parsing after an unterminated quoted attribute value and still parses a later valid binding',
    function () {
        $parser = new Parser();

        $source = '@event[title:"Unclosed] and @person[jane-austen](Jane Austen)';
        $result = $parser->parse($source);

        expect($result->hasDiagnostics())->toBeTrue()
            ->and($result->hasErrors())->toBeTrue()
            ->and($result->getDiagnostics())->toHaveCount(1);

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(2)
            ->and($children[0])->toBeInstanceOf(TextNode::class)
            ->and($children[0]->getText())->toBe('@event[title:"Unclosed] and ')
            ->and($children[1])->toBeInstanceOf(BindingNode::class)
            ->and($children[1]->getBindingType())->toBe('person')
            ->and($children[1]->getLabel())->toBe('Jane Austen');
    }
);

it(
    'silently falls back to text for an unterminated payload',
    function () {
        $parser = new Parser();

        $source = '@person[jane-austen';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(TextNode::class)
            ->and($children[0]->getText())->toBe($source);
    }
);

it(
    'silently falls back to text for an unterminated label',
    function () {
        $parser = new Parser();

        $source = '@person[jane-austen](Jane Austen';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(TextNode::class)
            ->and($children[0]->getText())->toBe($source);
    }
);

it(
    'silently falls back to text for an empty payload',
    function () {
        $parser = new Parser();

        $source = '@person[]';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(TextNode::class)
            ->and($children[0]->getText())->toBe($source);
    }
);

it(
    'assigns correct spans to text and binding nodes in mixed content',
    function () {
        $parser = new Parser();

        $source = 'Hello @person[jane-austen](Jane Austen) world.';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(3)
            ->and($children[0])->toBeInstanceOf(TextNode::class)
            ->and($children[1])->toBeInstanceOf(BindingNode::class)
            ->and($children[2])->toBeInstanceOf(TextNode::class);

        expect($children[0]->getSpan()->extract($source))->toBe('Hello ')
            ->and($children[1]->getSpan()->extract($source))->toBe('@person[jane-austen](Jane Austen)')
            ->and($children[2]->getSpan()->extract($source))->toBe(' world.');
    }
);

it(
    'assigns the correct span to a shorthand payload',
    function () {
        $parser = new Parser();

        $source = '@person[jane-austen](Jane Austen)';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(BindingNode::class);

        $payload = $children[0]->getPayload();

        expect($payload)->toBeInstanceOf(\ConundrumCodex\BindingEngine\Parser\Ast\Nodes\ShorthandPayloadNode::class)
            ->and($payload->getSpan()->extract($source))->toBe('jane-austen');
    }
);

it(
    'assigns correct spans to attribute list payloads and attribute assignments',
    function () {
        $parser = new Parser();

        $source = '@event[type:birth, subject:jane-austen]';
        $result = $parser->parse($source);

        expect($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();

        $children = $result->getDocument()->getChildren();

        expect($children)->toHaveCount(1)
            ->and($children[0])->toBeInstanceOf(BindingNode::class);

        $payload = $children[0]->getPayload();

        expect($payload)->toBeInstanceOf(AttributeListPayloadNode::class)
            ->and($payload->getSpan()->extract($source))->toBe('type:birth, subject:jane-austen')
            ->and($payload->getAttributes())->toHaveCount(2)
            ->and($payload->getAttributes()[0]->getSpan()->extract($source))->toBe('type:birth')
            ->and($payload->getAttributes()[1]->getSpan()->extract($source))->toBe('subject:jane-austen');
    }
);

it(
    'assigns a diagnostic span to the malformed attribute segment',
    function () {
        $parser = new Parser();

        $source = '@event[type!:birth, subject:jane-austen]';
        $result = $parser->parse($source);

        expect($result->hasDiagnostics())->toBeTrue()
            ->and($result->hasErrors())->toBeTrue()
            ->and($result->getDiagnostics())->toHaveCount(1);

        $diagnostic = $result->getDiagnostics()[0];

        expect($diagnostic->getSourceSpan())->not->toBeNull()
            ->and($diagnostic->getSourceSpan()->extract($source))->toBe('type!');
    }
);

it(
    'assigns a diagnostic span to an unterminated quoted value segment',
    function () {
        $parser = new Parser();

        $source = '@event[title:"Unclosed, type:birth]';
        $result = $parser->parse($source);

        expect($result->hasDiagnostics())->toBeTrue()
            ->and($result->hasErrors())->toBeTrue()
            ->and($result->getDiagnostics())->toHaveCount(1);

        $diagnostic = $result->getDiagnostics()[0];

        expect($diagnostic->getSourceSpan())->not->toBeNull()
            ->and($diagnostic->getSourceSpan()->extract($source))->toBe('title:"Unclosed, type:birth');
    }
);