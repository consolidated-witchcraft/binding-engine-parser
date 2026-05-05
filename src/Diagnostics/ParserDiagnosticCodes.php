<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Parser\Diagnostics;

final class ParserDiagnosticCodes
{
    public const CUSTOM_LINK_INVALID_COMPONENTS = 'parser.custom_link.invalid_components';
    public const BINDING_UNTERMINATED = 'parser.binding.unterminated';
    public const ATTRIBUTE_LIST_INVALID = 'parser.attribute_list.invalid';

    private function __construct()
    {
    }
}
