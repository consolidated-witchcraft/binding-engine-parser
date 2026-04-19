<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Diagnostics\Interfaces;

interface DiagnosticCodeInterface
{
    public function getCode(): string;
}
