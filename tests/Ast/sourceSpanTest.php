<?php

use ConundrumCodex\BindingEngine\Parser\Ast\Exceptions\SourceSpanConstructionException;
use ConundrumCodex\BindingEngine\Parser\Ast\SourceSpan;

\it(
    'constructs correctly',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $start = 1;
        $end = 300;
        $span = new SourceSpan($start, $end);
        \expect($span)->toBeInstanceOf(SourceSpan::class);
        \expect($span->start())->toBe($start);
        \expect($span->end())->toBe($end);
        \expect($span->length())->toBe(299);
        \expect($span->contains(150))->toBeTrue();
        \expect($span->contains(500))->toBeFalse();
        \expect((string) $span)->toBe('[1, 300)');
    }
);

\it(
    'rejects invalid span definitions',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        \expect(fn () => new SourceSpan(1, 0))->toThrow(SourceSpanConstructionException::class);
        \expect(fn () => new SourceSpan(-1, 1))->toThrow(SourceSpanConstructionException::class);
    }
);

\it(
    'performs span comparisons correctly',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $span = new SourceSpan(100, 500);
        $equalsSpan = new SourceSpan(100, 500);
        $containedSpan  = new SourceSpan(200, 300);
        $overlappingSpan  = new SourceSpan(400, 600);
        \expect($span->containsSpan($containedSpan))->toBeTrue();
        \expect($span->overlapsSpan($containedSpan))->toBeTrue();
        \expect($span->equalsSpan($containedSpan))->toBeFalse();

        \expect($span->containsSpan($overlappingSpan))->toBeFalse();
        \expect($span->overlapsSpan($overlappingSpan))->toBeTrue();
        \expect($span->equalsSpan($overlappingSpan))->toBeFalse();


        \expect($span->containsSpan($equalsSpan))->toBeTrue();
        \expect($span->overlapsSpan($equalsSpan))->toBeTrue();
        \expect($span->equalsSpan($equalsSpan))->toBeTrue();

    }
);

\it(
    'extracts text correctly',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $sourceText =
            <<<TEXT
            Call me Ishmael. 
            Some years ago - never mind how long precisely - having little or no money in my purse, 
            and nothing particular to interest me on shore, I thought I would sail about a little and see the watery part of the world.
            TEXT;
        $span = new SourceSpan(4, 18);
        $extractedText = $span->extract($sourceText);
        \expect($extractedText)->toBe(" me Ishmael. \n");
    }
);

\it(
    'treats spans as half-open intervals',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $span = new SourceSpan(10, 20);

        \expect($span->contains(10))->toBeTrue()
            ->and($span->contains(19))->toBeTrue()
            ->and($span->contains(20))->toBeFalse();
    }
);

\it(
    'allows zero-length spans',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $span = new SourceSpan(5, 5);

        \expect($span->length())->toBe(0)
            ->and($span->contains(5))->toBeFalse()
            ->and((string) $span)->toBe('[5, 5)');
    }
);

\it(
    'does not treat touching spans as overlapping',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $a = new SourceSpan(100, 200);
        $b = new SourceSpan(200, 300);

        \expect($a->overlapsSpan($b))->toBeFalse()
            ->and($b->overlapsSpan($a))->toBeFalse();
    }
);

\it(
    'handles containment symmetry correctly',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $outer = new SourceSpan(100, 500);
        $inner = new SourceSpan(200, 300);

        \expect($outer->containsSpan($inner))->toBeTrue()
            ->and($inner->containsSpan($outer))->toBeFalse();
    }
);

\it(
    'extracts an empty string for zero-length spans',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $span = new SourceSpan(3, 3);

        \expect($span->extract('abcdef'))->toBe('');
    }
);

\it(
    'does not treat zero-length spans as overlapping themselves',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $span = new SourceSpan(5, 5);

        \expect($span->overlapsSpan($span))->toBeFalse();
    }
);

\it(
    'does not treat zero-length spans as overlapping non-empty spans at the same offset',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $zeroLengthSpan = new SourceSpan(5, 5);
        $nonEmptySpan = new SourceSpan(5, 10);

        \expect($zeroLengthSpan->overlapsSpan($nonEmptySpan))->toBeFalse()
            ->and($nonEmptySpan->overlapsSpan($zeroLengthSpan))->toBeFalse();
    }
);

\it(
    'does not treat zero-length spans as overlapping enclosing spans',
    /**
     * @throws SourceSpanConstructionException
     */
    function () {
        $zeroLengthSpan = new SourceSpan(7, 7);
        $enclosingSpan = new SourceSpan(5, 10);

        \expect($zeroLengthSpan->overlapsSpan($enclosingSpan))->toBeFalse()
            ->and($enclosingSpan->overlapsSpan($zeroLengthSpan))->toBeFalse();
    }
);