<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser\Language;

final class IdentifierPatterns
{
    public const string BINDING_TYPE = '/^[a-z](?:[a-z0-9]|-(?=[a-z0-9])){2,63}$/';
    public const string ATTRIBUTE_IDENTIFIER = '/^[a-z](?:[a-z0-9]|-(?=[a-z0-9])){2,63}$/';
    public const string ATTRIBUTE_SEPARATOR = ':';         // key:value
    public const string ATTRIBUTE_LIST_SEPARATOR = ',';    // key:value, key:value
    public const string BINDING_INCIPIT_CHARACTER = '@';

    private function __construct()
    {
    }
}
