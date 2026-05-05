<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Interfaces;

interface DiagnosticCodeInterface
{
    public function getCode(): string;
}
