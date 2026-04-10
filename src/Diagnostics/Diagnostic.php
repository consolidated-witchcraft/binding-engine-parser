<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Diagnostics;

use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Exceptions\DiagnosticConstructionException;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Interfaces\DiagnosticInterface;

readonly class Diagnostic implements DiagnosticInterface
{
    /**
     * @throws DiagnosticConstructionException
     */
    public function __construct(
        private string $message,
        private string $code,
        private DiagnosticSeverityEnum $severity,
        private ?SourceSpan $sourceSpan = null,
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

    public function getSourceSpan(): ?SourceSpan
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
}
