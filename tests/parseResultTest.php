<?php

declare(strict_types=1);

use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\DocumentNode;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Diagnostic;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Exceptions\DiagnosticConstructionException;
use ConundrumCodex\BindingEngine\Parser\ParseResult;

it(
    'constructs correctly without diagnostics',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $document = new DocumentNode(
            span: new SourceSpan(0, 0),
            children: [],
        );

        $result = new ParseResult(
            document: $document,
        );

        expect($result->getDocument())->toBe($document)
            ->and($result->getDiagnostics())->toBe([])
            ->and($result->hasDiagnostics())->toBeFalse()
            ->and($result->hasErrors())->toBeFalse();
    }
);

it(
    'constructs correctly with diagnostics',
    /**
     * @throws DiagnosticConstructionException
     * @throws SourceSpanConstructionException
     */
    function () {
        $document = new DocumentNode(
            span: new SourceSpan(0, 10),
            children: [],
        );

        $diagnostic = new Diagnostic(
            message: 'Test warning.',
            code: 'parser.test.warning',
            severity: DiagnosticSeverityEnum::Warning,
            sourceSpan: new SourceSpan(2, 4),
        );

        $result = new ParseResult(
            document: $document,
            diagnostics: [$diagnostic],
        );

        expect($result->getDocument())->toBe($document)
            ->and($result->getDiagnostics())->toBe([$diagnostic])
            ->and($result->hasDiagnostics())->toBeTrue()
            ->and($result->hasErrors())->toBeFalse();
    }
);

it(
    'reports errors when at least one diagnostic is an error',
    /**
     * @throws DiagnosticConstructionException
     * @throws SourceSpanConstructionException
     */
    function () {
        $document = new DocumentNode(
            span: new SourceSpan(0, 10),
            children: [],
        );

        $warning = new Diagnostic(
            message: 'Test warning.',
            code: 'parser.test.warning',
            severity: DiagnosticSeverityEnum::Warning,
            sourceSpan: new SourceSpan(1, 2),
        );

        $error = new Diagnostic(
            message: 'Test error.',
            code: 'parser.test.error',
            severity: DiagnosticSeverityEnum::Error,
            sourceSpan: new SourceSpan(3, 5),
        );

        $result = new ParseResult(
            document: $document,
            diagnostics: [$warning, $error],
        );

        expect($result->hasDiagnostics())->toBeTrue()
            ->and($result->hasErrors())->toBeTrue()
            ->and($result->getDiagnostics())->toBe([$warning, $error]);
    }
);
