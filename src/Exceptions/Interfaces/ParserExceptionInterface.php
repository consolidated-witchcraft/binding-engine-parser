<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Exceptions\Interfaces;

use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;

interface ParserExceptionInterface extends \Throwable
{
    public function getDiagnosticCode(): string;

    public function getDiagnosticSeverity(): DiagnosticSeverityEnum;

    public function getSourceSpan(): ?SourceSpanInterface;
}