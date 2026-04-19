<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Exceptions;

class SourceSpanConstructionException extends AbstractAstException
{
    public function __construct(string $message, int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
