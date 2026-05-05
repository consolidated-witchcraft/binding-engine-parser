<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\Exceptions;

use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;

abstract class AbstractNodeException extends \Exception
{
    public function __construct(
        string $message,
        private readonly SourceSpanInterface $sourceSpan,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(sprintf("%s, Span: %s", $message, $sourceSpan), $code, $previous);
    }

    public function getSourceSpan(): SourceSpanInterface
    {
        return $this->sourceSpan;
    }
}
