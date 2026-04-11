<?php

declare(strict_types=1);

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\BindingNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\TextNode;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConundrumCodex\BindingEngine\Parser\Parser;

it(
        'treats an unterminated payload as text without crashing',
        function () {
            $parser = new Parser();

            $source = '@person[jane-austen';
            $result = $parser->parse($source);

            expect($result->getDocument()->getChildren())->toHaveCount(1)
                    ->and($result->getDocument()->getChildren()[0])->toBeInstanceOf(TextNode::class)
                    ->and($result->getDocument()->getChildren()[0]->getText())->toBe($source);
        }
);

it(
        'treats an unterminated label as text without crashing',
        function () {
            $parser = new Parser();

            $source = '@person[jane-austen](Jane Austen';
            $result = $parser->parse($source);

            expect($result->getDocument()->getChildren())->toHaveCount(1)
                    ->and($result->getDocument()->getChildren()[0])->toBeInstanceOf(TextNode::class)
                    ->and($result->getDocument()->getChildren()[0]->getText())->toBe($source);
        }
);

it(
        'treats an empty payload as text without crashing',
        function () {
            $parser = new Parser();

            $source = '@person[]';
            $result = $parser->parse($source);

            expect($result->getDocument()->getChildren())->toHaveCount(1)
                    ->and($result->getDocument()->getChildren()[0])->toBeInstanceOf(TextNode::class)
                    ->and($result->getDocument()->getChildren()[0]->getText())->toBe($source);
        }
);

it(
        'creates a diagnostic for a malformed attribute assignment with a missing separator',
        function () {
            $parser = new Parser();

            $source = '@event[typebirth, subject:jane-austen]';
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
        'creates a diagnostic for an invalid attribute identifier',
        function () {
            $parser = new Parser();

            $source = '@event[type!:birth, subject:jane-austen]';
            $result = $parser->parse($source);

            expect($result->hasDiagnostics())->toBeTrue()
                    ->and($result->hasErrors())->toBeTrue()
                    ->and($result->getDiagnostics())->toHaveCount(1);

            $diagnostic = $result->getDiagnostics()[0];

            expect($diagnostic->getSeverity())->toBe(DiagnosticSeverityEnum::Error)
                    ->and($diagnostic->getCode())->toBe('parser.attribute_identifier.invalid')
                    ->and($diagnostic->getSourceSpan())->not->toBeNull();

            $children = $result->getDocument()->getChildren();

            expect($children)->toHaveCount(1)
                    ->and($children[0])->toBeInstanceOf(TextNode::class)
                    ->and($children[0]->getText())->toBe($source);
        }
);

it(
        'continues parsing and still recognises later valid bindings after an unterminated payload',
        function () {
            $parser = new Parser();

            $source = '@person[jane-austen and @person[mary-shelley](Mary Shelley)';
            $result = $parser->parse($source);

            $children = $result->getDocument()->getChildren();

            expect($children)->toHaveCount(2)
                    ->and($children[0])->toBeInstanceOf(TextNode::class)
                    ->and($children[0]->getText())->toBe('@person[jane-austen and ')
                    ->and($children[1])->toBeInstanceOf(BindingNode::class)
                    ->and($children[1]->getBindingType())->toBe('person')
                    ->and($children[1]->getLabel())->toBe('Mary Shelley');
        }
);

it(
        'continues parsing after a malformed attribute binding and still parses a later valid binding',
        function () {
            $parser = new Parser();

            $source = '@event[type!:birth] and @person[jane-austen](Jane Austen)';
            $result = $parser->parse($source);

            expect($result->hasDiagnostics())->toBeTrue()
                    ->and($result->hasErrors())->toBeTrue()
                    ->and($result->getDiagnostics())->toHaveCount(1);

            $children = $result->getDocument()->getChildren();

            expect($children)->toHaveCount(2)
                    ->and($children[0])->toBeInstanceOf(TextNode::class)
                    ->and($children[0]->getText())->toBe('@event[type!:birth] and ')
                    ->and($children[1])->toBeInstanceOf(BindingNode::class)
                    ->and($children[1]->getBindingType())->toBe('person')
                    ->and($children[1]->getLabel())->toBe('Jane Austen');
        }
);

it(
        'parses duplicate attributes at parser level without error',
        function () {
            $parser = new Parser();

            $source = '@event[type:birth, type:death]';
            $result = $parser->parse($source);

            expect($result->getDiagnostics())->toBe([])
                    ->and($result->hasDiagnostics())->toBeFalse()
                    ->and($result->hasErrors())->toBeFalse();

            $children = $result->getDocument()->getChildren();

            expect($children)->toHaveCount(1)
                    ->and($children[0])->toBeInstanceOf(BindingNode::class);

            $payload = $children[0]->getPayload();

            expect($payload->getAttributes())->toHaveCount(2)
                    ->and($payload->getAttributes()[0]->getIdentifier())->toBe('type')
                    ->and($payload->getAttributes()[1]->getIdentifier())->toBe('type');
        }
);

it(
        'parses attribute lists with flexible whitespace',
        function () {
            $parser = new Parser();

            $source = '@event[ type:birth ,   subject:jane-austen ]';
            $result = $parser->parse($source);

            expect($result->getDiagnostics())->toBe([])
                    ->and($result->hasDiagnostics())->toBeFalse()
                    ->and($result->hasErrors())->toBeFalse();

            $children = $result->getDocument()->getChildren();

            expect($children)->toHaveCount(1)
                    ->and($children[0])->toBeInstanceOf(BindingNode::class);

            $payload = $children[0]->getPayload();

            expect($payload->getAttributes())->toHaveCount(2)
                    ->and($payload->getAttributes()[0]->getIdentifier())->toBe('type')
                    ->and($payload->getAttributes()[0]->getValue())->toBe('birth')
                    ->and($payload->getAttributes()[1]->getIdentifier())->toBe('subject')
                    ->and($payload->getAttributes()[1]->getValue())->toBe('jane-austen');
        }
);

it(
        'parses quoted attribute values',
        function () {
            $parser = new Parser();

            $source = '@event[title:"The Fall of Gondolin"]';
            $result = $parser->parse($source);

            expect($result->hasErrors())->toBeFalse();

            $payload = $result->getDocument()->getChildren()[0]->getPayload();

            expect($payload->getAttributes()[0]->getValue())
                    ->toBe('The Fall of Gondolin');
        }
);

it(
        'allows commas inside quoted values',
        function () {
            $parser = new Parser();

            $source = '@event[note:"a, b, c", type:birth]';
            $result = $parser->parse($source);

            $payload = $result->getDocument()->getChildren()[0]->getPayload();

            expect($payload->getAttributes())->toHaveCount(2)
                    ->and($payload->getAttributes()[0]->getValue())->toBe('a, b, c');
        }
);

it(
        'creates a diagnostic for unterminated quoted value',
        function () {
            $parser = new Parser();

            $source = '@event[title:"Unclosed]';
            $result = $parser->parse($source);

            expect($result->hasErrors())->toBeTrue();
        }
);