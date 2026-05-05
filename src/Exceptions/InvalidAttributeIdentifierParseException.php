<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Exceptions;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;

final class InvalidAttributeIdentifierParseException extends AbstractParserException
{
    public function __construct(
        string $message = 'Invalid attribute identifier.',
        ?SourceSpanInterface $sourceSpan = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: $message,
            diagnosticCode: 'parser.attribute_identifier.invalid',
            diagnosticSeverity: DiagnosticSeverityEnum::Error,
            sourceSpan: $sourceSpan,
            previous: $previous,
        );
    }
}
