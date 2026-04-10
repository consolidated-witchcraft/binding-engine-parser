<?php

use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Diagnostic;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Exceptions\DiagnosticConstructionException;

\it(
    'rejects invalid constructors',
    function (string $invalidString) {
        \expect(
            /**
             * @throws DiagnosticConstructionException
             */
            function () use ($invalidString) {
                new Diagnostic($invalidString, 'code', DiagnosticSeverityEnum::Error);
        })->toThrow(DiagnosticConstructionException::class);

        \expect(
            /**
             * @throws DiagnosticConstructionException
             */
            function () use ($invalidString) {
                new Diagnostic('message', $invalidString, DiagnosticSeverityEnum::Error);
        })->toThrow(DiagnosticConstructionException::class);
    }
)->with(
    function (): iterable {
        yield 'empty string' => '';
        yield 'all whitespace' => '     ';
    }
);

\it(
    'constructs correctly without a source span',
    /**
     * @throws DiagnosticConstructionException
     */
    function () {
        $message = 'Test Message';
        $code = 'parser.test.error';
        $diagnostic = new Diagnostic($message, $code, DiagnosticSeverityEnum::Error);

        expect($diagnostic)->toBeInstanceOf(Diagnostic::class)
            ->and($diagnostic->getMessage())->toBe($message)
            ->and($diagnostic->getCode())->toBe($code)
            ->and($diagnostic->getSeverity())->toBe(DiagnosticSeverityEnum::Error)
            ->and($diagnostic->getSourceSpan())->toBeNull();
    }
);

\it(
    'constructs correctly with a source span',
    /**
     * @throws DiagnosticConstructionException
     * @throws SourceSpanConstructionException
     */
    function () {
        $message = 'Test Message';
        $code = 'parser.test.warning';
        $sourceSpan = new SourceSpan(
            start: 5,
            end: 150
        );
        $diagnostic = new Diagnostic($message, $code, DiagnosticSeverityEnum::Warning, $sourceSpan);

        expect($diagnostic)->toBeInstanceOf(Diagnostic::class)
            ->and($diagnostic->getMessage())->toBe($message)
            ->and($diagnostic->getCode())->toBe($code)
            ->and($diagnostic->getSeverity())->toBe(DiagnosticSeverityEnum::Warning)
            ->and($diagnostic->getSourceSpan())->toBe($sourceSpan);
    }
);