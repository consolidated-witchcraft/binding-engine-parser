<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser;

use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\DocumentNode;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Enums\DiagnosticSeverityEnum;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Interfaces\DiagnosticInterface;

readonly class ParseResult
{
    /**
     * @param list<DiagnosticInterface> $diagnostics
     */
    public function __construct(
        private DocumentNode $document,
        private array $diagnostics = [],
    ) {
    }

    public function getDocument(): DocumentNode
    {
        return $this->document;
    }

    /**
     * @return list<DiagnosticInterface>
     */
    public function getDiagnostics(): array
    {
        return $this->diagnostics;
    }

    public function hasDiagnostics(): bool
    {
        return $this->diagnostics !== [];
    }

    public function hasErrors(): bool
    {
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->getSeverity() === DiagnosticSeverityEnum::Error) {
                return true;
            }
        }

        return false;
    }
}
