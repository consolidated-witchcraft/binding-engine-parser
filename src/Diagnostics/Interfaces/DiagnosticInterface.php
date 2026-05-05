<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;

interface DiagnosticInterface
{
    public function getMessage(): string;

    public function getCode(): string;

    public function getSeverity(): DiagnosticSeverityEnum;

    public function getSourceSpan(): ?SourceSpanInterface;
}
