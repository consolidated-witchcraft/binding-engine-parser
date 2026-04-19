<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Diagnostics\Interfaces;

use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;

interface DiagnosticInterface
{
    public function getMessage(): string;

    public function getCode(): string;

    public function getSeverity(): DiagnosticSeverityEnum;

    public function getSourceSpan(): ?SourceSpanInterface;
}
