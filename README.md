# BindingEngine

BindingEngine enriches CommonMark-compatible markdown with a semantic linking dialect.
It parses these bindings into an abstract syntax tree (AST), which can be used to build and maintain complex relationships.

## Parser
This library is the parser library. It is responsible solely for detecting bindings in text and parsing them into an AST.
It checks for syntactical correctness, but not semantic validity; that's the responsibility of the Vocabulary library.


## How it works
```text
Jane Austen was an English novelist.
        
She was born on @event[
    type: birth,
    location: steventon-hampshire,
    subject: jane-austen,
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
```
The parser detects the bindings (`@something`), parses their payloads and produces an AST.

### Core principle
Bindings separate presentation from meaning:
- Labels are for people
- Payloads are for systems

## Binding design philosophy
### Payload forms
There are two payload forms: Expanded and Short-form.

#### Expanded
Expanded bindings have attributes, which are expressed as a comma-separated list of `key: value` pairs. Newlines are optional, but recommended for legibility.
Example:
```text
@event[
    type: birth,
    location: portsmouth,
    subject: charles-dickens,
    date: 1812-02-07
](born)
```
#### Short-form
Short-form is syntactic sugar for a single `value` attribute.
`@date[1066-12-14]` is equivalent to `@date[value: 1066-12-14]`

Both of these forms are valid:

```text
@date[1066-12-14](1066)
```

```text
@date[date: 1066-12-14](1066)
```

### Binding components
Every binding has three parts:
- binding type
- payload
- label

For example:
```text
@person[jane-austen](Jane Austen)
```
- binding type: person
- payload: jane-austen
- label: Jane Austen

And:
```text
@event[
    type: birth,
    subject: jane-austen,
    date: 1775-12-16,
    location: steventon-hampshire
](born)
```

- binding type: event
- payload: structured attribute map
- label: born

### Parsing Rules
- If a payload contains an unquoted colon `:`, it is interpreted as an attribute list.
- Values containing reserved characters (`:`, `,`, `]`, or line breaks) MUST be quoted using double quotes.
- Whitespace is ignored around keys, values, and separators.
- Newlines are treated as separators.
- Payloads cannot be nested.

## AST output
The parser produces an AST containing ordinary text nodes and structured binding nodes.

Typical node types include:
- document
- paragraph
- text
- binding

Downstream libraries may then consume this AST for validation, graph projection, or inference.

## Development
Code style is enforced with `php-cs-fixer`.

Check formatting without changing files:
```bash
composer cs:check
```

Apply formatting:
```bash
composer cs:fix
```

## Formal Grammar

```ebnf
Binding         ::= "@" Identifier Payload Label ;

Identifier      ::= Letter ( Letter | Digit | "-" )* ;

Payload         ::= "[" PayloadContent "]" ;

PayloadContent  ::= AttributeList | ShorthandValue ;

AttributeList   ::= Attribute ( Separator Attribute )* [ Separator ] ;

Attribute       ::= Key ":" Value ;

Key             ::= Identifier ;
Value           ::= QuotedString | BareValue ;

ShorthandValue  ::= Value ;

Label           ::= "(" LabelText ")" ;

LabelText       ::= { Character - ")" } ;

Separator       ::= "," | LineBreak ;

LineBreak       ::= "\n" | "\r\n" ;

BareValue       ::= { Character - "," - "]" - LineBreak } ;

QuotedString    ::= '"' { QuotedCharacter } '"' ;

QuotedCharacter ::= EscapedCharacter | UnescapedCharacter ;

EscapedCharacter ::= "\" '"' | "\" "\" | "\" "n" | "\" "r" | "\" "t" ;

UnescapedCharacter ::= Character - '"' - "\" ;

Letter          ::= "a"…"z" | "A"…"Z" ;
Digit           ::= "0"…"9" ;

Character       ::= ? any Unicode character ? ;
```
### Disambiguation

If a payload contains an unquoted colon (`:`), it is parsed as an `AttributeList`.  
Otherwise, it is parsed as a `ShorthandValue`.
