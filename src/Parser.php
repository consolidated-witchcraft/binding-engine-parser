<?php

declare(strict_types=1);

namespace ConundrumCodex\BindingEngine\Parser;

use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\AttributeAssignmentNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\AttributeListPayloadNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\BindingNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\DocumentNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidAttributeAssignmentNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidAttributeListPayloadNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidBindingNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidShorthandPayloadNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Exceptions\InvalidTextNodeException;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\AstNodeInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\Interfaces\BindingPayloadInterface;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\ShorthandPayloadNode;
use ConundrumCodex\BindingEngine\Parser\Ast\Nodes\TextNode;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;
use ConundrumCodex\BindingEngine\Parser\Diagnostics\Diagnostic;
use ConundrumCodex\BindingEngine\Parser\Exceptions\Interfaces\ParserExceptionInterface;
use ConundrumCodex\BindingEngine\Parser\Exceptions\InvalidAttributeIdentifierParseException;
use ConundrumCodex\BindingEngine\Parser\Exceptions\MalformedAttributeAssignmentException;
use ConundrumCodex\BindingEngine\Parser\Language\IdentifierPatterns;

final class Parser
{
    /**
     * @param string $source
     * @return ParseResult
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     * @throws InvalidBindingNodeException
     * @throws InvalidShorthandPayloadNodeException
     * @throws InvalidTextNodeException
     * @throws SourceSpanConstructionException
     * @throws Diagnostics\Exceptions\DiagnosticConstructionException
     */
    public function parse(string $source): ParseResult
    {
        $length = strlen($source);
        $offset = 0;
        $textBuffer = '';
        $textBufferStart = 0;

        /** @var list<AstNodeInterface> $children */
        $children = [];
        $diagnostics = [];

        while ($offset < $length) {
            if ($source[$offset] !== IdentifierPatterns::BINDING_INCIPIT_CHARACTER) {
                if ($textBuffer === '') {
                    $textBufferStart = $offset;
                }

                $textBuffer .= $source[$offset];
                $offset++;
                continue;
            }

            try {
                $bindingResult = $this->tryParseBinding($source, $offset);
            } catch (ParserExceptionInterface $exception) {
                $diagnostics[] = Diagnostic::makeFromParserException($exception);

                if ($textBuffer === '') {
                    $textBufferStart = $offset;
                }

                $textBuffer .= $source[$offset];
                $offset++;
                continue;
            }

            if ($bindingResult === null) {
                if ($textBuffer === '') {
                    $textBufferStart = $offset;
                }

                $textBuffer .= $source[$offset];
                $offset++;
                continue;
            }

            if ($textBuffer !== '') {
                $children[] = new TextNode(
                    span: new SourceSpan($textBufferStart, $bindingResult['start']),
                    text: $textBuffer,
                );

                $textBuffer = '';
            }

            $children[] = $bindingResult['node'];
            $offset = $bindingResult['nextOffset'];
        }

        if ($textBuffer !== '') {
            $children[] = new TextNode(
                span: new SourceSpan($textBufferStart, $length),
                text: $textBuffer,
            );
        }

        $document = new DocumentNode(
            span: new SourceSpan(0, $length),
            children: $children,
        );

        return new ParseResult(
            document: $document,
            diagnostics: $diagnostics,
        );
    }

    /**
     * @return array{
     *     start:int,
     *     nextOffset:int,
     *     node:BindingNode
     * }|null
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     * @throws InvalidBindingNodeException
     * @throws InvalidShorthandPayloadNodeException
     * @throws SourceSpanConstructionException
     */
    private function tryParseBinding(string $source, int $offset): ?array
    {
        $length = strlen($source);
        $start = $offset;

        if (($source[$offset] ?? null) !== IdentifierPatterns::BINDING_INCIPIT_CHARACTER) {
            return null;
        }

        $offset++;

        $bindingTypeStart = $offset;

        while (
            $offset < $length
            && preg_match('/[a-z0-9-]/', $source[$offset]) === 1
        ) {
            $offset++;
        }

        $bindingType = substr($source, $bindingTypeStart, $offset - $bindingTypeStart);

        if ($bindingType === '' || preg_match(IdentifierPatterns::BINDING_TYPE, $bindingType) !== 1) {
            return null;
        }

        if (($source[$offset] ?? null) !== '[') {
            return null;
        }

        $payloadOpenOffset = $offset;
        $payloadCloseOffset = $this->findMatchingBracket($source, $payloadOpenOffset, '[', ']');

        if ($payloadCloseOffset === null) {
            return null;
        }

        $payloadRaw = substr(
            $source,
            $payloadOpenOffset + 1,
            $payloadCloseOffset - $payloadOpenOffset - 1,
        );

        if ($payloadRaw === '') {
            return null;
        }

        $payloadSpan = new SourceSpan($payloadOpenOffset + 1, $payloadCloseOffset);
        $payload = $this->parsePayload($payloadRaw, $payloadSpan);

        $offset = $payloadCloseOffset + 1;

        $label = null;

        if (($source[$offset] ?? null) === '(') {
            $labelOpenOffset = $offset;
            $labelCloseOffset = $this->findMatchingParen($source, $labelOpenOffset);

            if ($labelCloseOffset === null) {
                return null;
            }

            $label = substr(
                $source,
                $labelOpenOffset + 1,
                $labelCloseOffset - $labelOpenOffset - 1,
            );

            $offset = $labelCloseOffset + 1;
        }

        $raw = substr($source, $start, $offset - $start);

        return [
            'start' => $start,
            'nextOffset' => $offset,
            'node' => new BindingNode(
                span: new SourceSpan($start, $offset),
                bindingType: $bindingType,
                payload: $payload,
                raw: $raw,
                label: $label,
            ),
        ];
    }

    /**
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     * @throws InvalidShorthandPayloadNodeException
     * @throws SourceSpanConstructionException
     */
    private function parsePayload(string $payloadRaw, SourceSpan $payloadSpan): BindingPayloadInterface
    {
        if (str_contains($payloadRaw, IdentifierPatterns::ATTRIBUTE_SEPARATOR)) {
            return $this->parseAttributeListPayload($payloadRaw, $payloadSpan);
        }

        return new ShorthandPayloadNode(
            span: $payloadSpan,
            value: $payloadRaw,
            raw: $payloadRaw,
        );
    }

    /**
     * @throws InvalidAttributeAssignmentNodeException
     * @throws InvalidAttributeListPayloadNodeException
     * @throws SourceSpanConstructionException
     * @throws MalformedAttributeAssignmentException
     * @throws InvalidAttributeIdentifierParseException
     */
    private function parseAttributeListPayload(string $payloadRaw, SourceSpan $payloadSpan): AttributeListPayloadNode
    {
        $parts = $this->splitAttributeParts($payloadRaw);

        $attributes = [];
        $cursor = $payloadSpan->getStart();

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $searchOffset = max(0, $cursor - $payloadSpan->getStart());

            $partStart = strpos($payloadRaw, $part, $searchOffset);
            if ($partStart === false) {
                $partStart = $searchOffset;
            }

            $absoluteStart = $payloadSpan->getStart() + $partStart;
            $absoluteEnd = $absoluteStart + strlen($part);
            $partSpan = new SourceSpan($absoluteStart, $absoluteEnd);

            $separatorOffset = strpos($part, IdentifierPatterns::ATTRIBUTE_SEPARATOR);

            if ($separatorOffset === false) {
                throw new MalformedAttributeAssignmentException(
                    message: sprintf(
                        'Malformed attribute assignment "%s".',
                        $part,
                    ),
                    sourceSpan: $partSpan,
                );
            }

            $identifier = trim(substr($part, 0, $separatorOffset));
            $valueRaw = trim(substr($part, $separatorOffset + 1));

            $value = $this->parseAttributeValue($valueRaw, $partSpan);

            $identifierStart = $absoluteStart;
            $identifierEnd = $identifierStart + strlen($identifier);
            $identifierSpan = new SourceSpan($identifierStart, $identifierEnd);

            if (preg_match(IdentifierPatterns::ATTRIBUTE_IDENTIFIER, $identifier) !== 1) {
                throw new InvalidAttributeIdentifierParseException(
                    message: sprintf(
                        'Invalid attribute identifier "%s".',
                        $identifier,
                    ),
                    sourceSpan: $identifierSpan,
                );
            }

            $attributes[] = new AttributeAssignmentNode(
                span: $partSpan,
                identifier: $identifier,
                value: $value,
                raw: $part,
            );

            $cursor = $absoluteEnd + strlen(IdentifierPatterns::ATTRIBUTE_LIST_SEPARATOR);
        }

        return new AttributeListPayloadNode(
            span: $payloadSpan,
            attributes: $attributes,
            raw: $payloadRaw,
        );
    }

    private function findMatchingBracket(string $source, int $openOffset, string $open, string $close): ?int
    {
        $length = strlen($source);
        $depth = 0;

        for ($i = $openOffset; $i < $length; $i++) {
            if ($source[$i] === $open) {
                $depth++;
                continue;
            }

            if ($source[$i] === $close) {
                $depth--;

                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return null;
    }

    private function findMatchingParen(string $source, int $openOffset): ?int
    {
        return $this->findMatchingBracket($source, $openOffset, '(', ')');
    }

    /**
     * @return list<string>
     */
    private function splitAttributeParts(string $input): array
    {
        $parts = [];
        $buffer = '';
        $inQuotes = false;

        $length = strlen($input);

        for ($i = 0; $i < $length; $i++) {
            $char = $input[$i];

            if ($char === '"') {
                $inQuotes = !$inQuotes;
                $buffer .= $char;
                continue;
            }

            if (
                $char === IdentifierPatterns::ATTRIBUTE_LIST_SEPARATOR
                && !$inQuotes
            ) {
                $parts[] = trim($buffer);
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        if ($buffer !== '') {
            $parts[] = trim($buffer);
        }

        return $parts;
    }

    /**
     * @throws MalformedAttributeAssignmentException
     */
    private function parseAttributeValue(string $valueRaw, SourceSpan $span): string
    {
        // Quoted value
        if (str_starts_with($valueRaw, '"')) {
            if (!str_ends_with($valueRaw, '"') || strlen($valueRaw) < 2) {
                throw new MalformedAttributeAssignmentException(
                    message: 'Unterminated quoted attribute value.',
                    sourceSpan: $span,
                );
            }

            return substr($valueRaw, 1, -1);
        }

        // Unquoted value
        return $valueRaw;
    }
}
