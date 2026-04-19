<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Exceptions;

use ConundrumCodex\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;

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
