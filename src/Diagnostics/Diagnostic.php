<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Exceptions\DiagnosticConstructionException;
use ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Interfaces\DiagnosticInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Exceptions\Interfaces\ParserExceptionInterface;

readonly class Diagnostic implements DiagnosticInterface
{
    /**
     * @throws DiagnosticConstructionException
     */
    public function __construct(
        private string $message,
        private string $code,
        private DiagnosticSeverityEnum $severity,
        private ?SourceSpanInterface $sourceSpan = null,
    ) {
        $this->guard();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSeverity(): DiagnosticSeverityEnum
    {
        return $this->severity;
    }

    public function getSourceSpan(): ?SourceSpanInterface
    {
        return $this->sourceSpan;
    }

    /**
     * @throws DiagnosticConstructionException
     */
    private function guard(): void
    {
        if (trim($this->message) === '') {
            throw new DiagnosticConstructionException('Diagnostic message must not be empty.');
        }

        if (trim($this->code) === '') {
            throw new DiagnosticConstructionException('Diagnostic code must not be empty.');
        }
    }

    /**
     * @throws DiagnosticConstructionException
     */
    public static function makeFromParserException(ParserExceptionInterface $exception): Diagnostic
    {
        return new Diagnostic(
            message: $exception->getMessage(),
            code: $exception->getDiagnosticCode(),
            severity: $exception->getDiagnosticSeverity(),
            sourceSpan: $exception->getSourceSpan(),
        );
    }

}
