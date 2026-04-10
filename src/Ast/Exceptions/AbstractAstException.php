<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Ast\Exceptions;

abstract class AbstractAstException extends \Exception
{
    public function __construct(string $message, int $code = 0, ?\Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}