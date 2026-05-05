<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Exceptions;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;

final class MalformedAttributeAssignmentException extends AbstractParserException
{
    public function __construct(
        string $message = 'Malformed attribute assignment.',
        ?SourceSpanInterface $sourceSpan = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: $message,
            diagnosticCode: 'parser.attribute_assignment.malformed',
            diagnosticSeverity: DiagnosticSeverityEnum::Error,
            sourceSpan: $sourceSpan,
            previous: $previous,
        );
    }
}
