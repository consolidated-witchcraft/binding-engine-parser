<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums;

enum DiagnosticSeverityEnum: string
{
    case Error = 'error';
    case Warning = 'warning';
    case Info = 'info';
}
