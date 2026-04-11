<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Exceptions;

use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConundrumCodex\BindingEngine\Parser\Exceptions\Interfaces\ParserExceptionInterface;
use RuntimeException;

abstract class AbstractParserException extends RuntimeException implements ParserExceptionInterface
{
    public function __construct(
        string $message,
        private readonly string $diagnosticCode,
        private readonly DiagnosticSeverityEnum $diagnosticSeverity,
        private readonly ?SourceSpanInterface $sourceSpan = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getDiagnosticCode(): string
    {
        return $this->diagnosticCode;
    }

    public function getDiagnosticSeverity(): DiagnosticSeverityEnum
    {
        return $this->diagnosticSeverity;
    }

    public function getSourceSpan(): ?SourceSpanInterface
    {
        return $this->sourceSpan;
    }
}