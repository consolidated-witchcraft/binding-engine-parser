<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Exceptions\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;

interface ParserExceptionInterface extends \Throwable
{
    public function getDiagnosticCode(): string;

    public function getDiagnosticSeverity(): DiagnosticSeverityEnum;

    public function getSourceSpan(): ?SourceSpanInterface;
}
