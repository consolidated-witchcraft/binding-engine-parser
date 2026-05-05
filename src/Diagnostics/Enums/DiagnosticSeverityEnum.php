<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics\Enums;

enum DiagnosticSeverityEnum: string
{
    case Error = 'error';
    case Warning = 'warning';
    case Info = 'info';
}
