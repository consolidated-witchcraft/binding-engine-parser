<?php

namespace Tests\Unit\Parser;

\it('parses_short_form_tags_correctly', function() {
    $sampleText = <<<TEXT
        Jane Austen was an English novelist.
        
        She was born on @event[
            type: birth,
            location: steventon-hampshire,
            subject: jane-austen
            date: 1775-12-16
        ](born) December 16, 1775 in @location[steventon-hampshire](Steventon, Hampshire).
        Her parents were @relationship[
            type: parent_of,
            subject: george-austen,
            object: jane-austen
        ](George) and @relationship[
            type: parent_of,
            subject: cassandra-austen,
            object: jane-austen
        ](Cassandra Austen).
        
        TEXT;

});