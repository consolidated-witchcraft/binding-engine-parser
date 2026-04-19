<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Diagnostics\Exceptions;

class DiagnosticConstructionException extends AbstractDiagnosticException
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
