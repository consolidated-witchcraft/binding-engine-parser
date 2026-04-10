# AGENTS.md


## Interpretation

The keywords **MUST**, **MUST NOT**, **REQUIRED**, **SHALL**, **SHALL NOT**, **SHOULD**, **SHOULD NOT**, **RECOMMENDED**, **NOT RECOMMENDED**, **MAY**, and **OPTIONAL** in this document are to be interpreted as described in RFC 2119.

---

## About this file
This file defines the general project rules. The documents under ./codingstandards/ are topic-specific normative standards and MUST be followed.
If a rule in ./codingstandards/ conflicts with this file, the more specific rule in ./codingstandards/ takes precedence for that topic.

## 1. Purpose

This project is a **PHP Composer library** for parsing a custom markdown dialect into an Abstract Syntax Tree and projecting that AST into a semantic graph model.

This project **MUST** be treated as:
- a parsing system
- a semantic projection system
- a graph-fact emission system

This project **MUST NOT** be treated as:
- a UI framework
- a persistence layer
- a renderer
- an application service container

Contributors **SHOULD** preserve the library’s role as a reusable engine with clear public contracts.

---

## 2. Architectural Boundaries

The system is divided into the following layers:

- `Parser`
- `Vocabulary`
- `Projector`
- `Graph`

These boundaries **MUST** remain explicit.

Code **MUST NOT** blur these concerns by embedding one layer’s responsibilities inside another without a compelling and documented reason.

### 2.1 Parser

The parser **MUST**:
- accept source text
- recognise syntax
- produce AST nodes
- preserve source span information
- emit parse diagnostics where needed

The parser **MUST NOT**:
- consult the vocabulary
- validate semantic meaning
- infer facts
- build graph facts
- perform persistence-related work

### 2.2 Vocabulary

The vocabulary layer **MUST** define the semantic vocabulary used by projection, including where applicable:
- entity types
- relation types
- event types
- inference rules
- inverse or symmetric relation behaviour
- type constraints

The vocabulary layer **SHOULD** be data-driven.

The vocabulary layer **MUST NOT** be bypassed by ad hoc hardcoded semantic rules in unrelated components, except where such behaviour is explicitly documented as a temporary limitation.

### 2.3 Projector

The projector **MUST**:
- walk the AST
- resolve semantic meaning against the vocabulary
- emit graph facts
- emit projection diagnostics
- preserve source provenance where possible

The projector **MUST NOT**:
- parse raw source text directly except for internal defensive checks
- redefine vocabulary semantics locally
- silently discard semantically invalid constructs without a diagnostic

### 2.4 Graph
The graph layer **MUST** define the semantic output model.

The graph layer **SHOULD** contain:
- entity references
- relation facts
- event facts
- derived facts
- provenance objects
- graph builder contracts or supporting types

The graph layer **MUST NOT**:
- parse markdown
- perform vocabulary resolution
- implement inference logic
- contain application-specific persistence concerns

---

## 3. Parsing Rules

The parser **MUST** be syntax-oriented rather than semantics-oriented.

If input is syntactically valid but semantically nonsensical, the parser **MUST** still produce an AST where reasonably possible.

For example, if a custom link is structurally valid but references an unknown entity type, that condition **MUST** be handled later by semantic projection or validation rather than by the parser.

The parser **SHOULD** be best-effort.

The parser **SHOULD NOT** fail completely when a partial AST can still be produced.

All AST nodes **MUST** preserve source location data sufficient for diagnostics and editor integration.

AST nodes representing custom syntax **SHOULD** preserve raw source text in addition to parsed fields.

---

## 4. Vocabulary Rules

Vocabulary definitions **MUST** be validated at load time.

Invalid vocabulary definitions **MUST** cause deterministic failure with a clear error.

Vocabulary definitions **SHOULD** be normalised into typed PHP objects rather than passed through the system as unstructured arrays.

The vocabulary system **MAY** support:
- a base vocabulary
- one or more extension vocabularies
- deterministic merge behaviour

If extension vocabularies are supported, merge behaviour **MUST** be defined explicitly.

Extension vocabularies **MUST NOT** silently override existing definitions unless such override behaviour is both supported and explicitly requested.

Duplicate keys **MUST** be handled deterministically.

---

## 5. Semantic Modelling Rules

### 5.1 Relations

Canonical relations **MUST** be represented directionally.

A relation **MUST** have a single authoritative direction in the form:

`subject -> predicate -> object`

Reverse views **MAY** be derived through vocabulary definitions.

Reverse views **MUST NOT** be stored as separate canonical facts merely for convenience.

### 5.2 Events

Occurrences such as births, deaths, assassinations, coronations, battles, and other time-bound happenings **SHOULD** be modelled as events rather than as simple relations.

Stable or enduring states such as membership, ownership, rulership, or location **SHOULD** generally be modelled as relations unless the design clearly requires an event representation.

When deciding between a relation and an event, contributors **SHOULD** ask whether the fact represents:
- a state
- or an occurrence

### 5.3 Derived Facts

The system **MUST** distinguish between:
- authored facts
- derived facts

Derived facts **MUST** preserve provenance.

Derived facts **MUST NOT** overwrite authored facts.

Derived facts **SHOULD** be recomputable from authoritative inputs.

Where authored facts and derived facts conflict, the conflict **SHOULD** result in a diagnostic or explicit conflict-handling strategy rather than silent replacement.

---

## 6. Projection Rules

Projection **MUST** be vocabulary-driven.

The projector **MUST** use the vocabulary to determine whether a parsed construct is semantically valid.

If a parsed construct is semantically invalid, the projector **MUST** emit a diagnostic.

The projector **SHOULD** continue projecting other valid content where possible.

The projector **SHOULD NOT** abort the entire projection process merely because one construct is invalid, unless continuing would produce unusable or misleading output.

The projector **MUST** preserve source provenance for emitted facts where reasonably possible.

The projector **MAY** apply inference rules, but if it does, those rules **SHOULD** be defined in or derived from the vocabulary rather than embedded as scattered implementation detail.

---

## 7. Diagnostics and Error Handling

Recoverable problems **SHOULD** be reported via diagnostics rather than exceptions.

Diagnostics **SHOULD** include, where possible:
- a human-readable message
- severity
- source span
- optional machine-readable code

Exceptions **SHOULD** be reserved for:
- invalid vocabulary definitions
- unrecoverable internal invariants
- unsupported usage that prevents safe continuation

The system **MUST NOT** use exceptions as ordinary control flow for expected invalid user content.

---

## 8. Public Contracts

Major extension points **SHOULD** be defined as interfaces.

These **SHOULD** include, where relevant:
- `ParserInterface`
- `ProjectorInterface`
- `GraphBuilderInterface`
- `VocabularyLoaderInterface`

Public interfaces **MUST** remain stable unless a versioned breaking change is intended.

Internal implementation details **SHOULD NOT** leak into public contracts without deliberate design review.

Consumers **MAY** provide their own graph builder implementations.

The library **MUST NOT** assume a single concrete persistence strategy.

---

## 9. Data Modelling and Value Objects

The codebase **SHOULD** prefer small, explicit value objects over unstructured associative arrays.

Important domain values such as:
- source spans
- entity references
- facts
- diagnostics
- provenance

**SHOULD** be represented as dedicated types.

Cross-layer “magic arrays” **SHOULD NOT** be used where a dedicated class or readonly DTO would materially improve correctness or maintainability.

Mutable shared state **SHOULD NOT** be introduced without strong justification.

Readonly value objects **SHOULD** be preferred where practical.

---

## 10. Directory and Namespace Structure

The source tree **SHOULD** reflect domain boundaries rather than incidental implementation detail.

A recommended structure is:

```text
src/
  Contract/
  Exception/
  Graph/
  Parser/
    Ast/
  Projector/
  Value/
  Vocabulary/
    Definition/
```
Namespaces SHOULD mirror this layout.

New code MUST be placed according to its responsibility rather than convenience.

For example:

AST node classes MUST live under Parser\Ast
vocabulary definitions MUST live under Vocabulary\Definition
graph fact types MUST live under Graph
AST-to-graph transformation logic MUST live under Projector

## 11. Testing Expectations

Parser changes MUST be accompanied by parser tests.

Vocabulary loading or validation changes MUST be accompanied by vocabulary tests.

Projection changes MUST be accompanied by projection tests.

Tests SHOULD cover:

valid syntax
malformed syntax
unknown vocabulary terms
invalid semantic combinations
partial recovery behaviour
provenance preservation where relevant

Contributors SHOULD prefer small, focused tests over broad tests with unclear failure causes.

## 12. Backward Compatibility

Public syntax changes MUST be treated as breaking unless explicitly designed for backward compatibility.

Vocabulary format changes SHOULD be versioned or migrated in a controlled way.

Public contracts MUST NOT change incompatibly in a patch release.

Where compatibility cannot be preserved, the change MUST be documented clearly.

## 13. Security and Trust Boundaries

All external input MUST be treated as untrusted.

Vocabulary files MUST be validated before being accepted as runtime definitions.

Parser and projector code MUST NOT assume that user-authored content is well-formed.

The library MUST NOT execute arbitrary code from vocabulary definitions or user content.

## 14. Performance Guidance

Correctness MUST take priority over premature optimisation.

However:

parsing SHOULD avoid unnecessary repeated work
projection SHOULD avoid quadratic behaviour where a linear or near-linear approach is feasible
vocabulary loading SHOULD normalise data once and reuse typed definitions thereafter

Performance shortcuts MUST NOT violate architectural boundaries.

## 15. What Contributors Must Not Do

Contributors MUST NOT:

mix parsing and semantic projection logic
bypass the vocabulary for new semantic behaviour
store both forward and reverse relations as separate canonical facts without explicit design approval
model event-like occurrences as plain edges for convenience where that would distort semantics
replace explicit value objects with opaque arrays merely to reduce typing
discard source span information from AST nodes
silently suppress invalid semantic constructs without diagnostics

## 16. Mental Model

Contributors SHOULD understand the system as a pipeline:
```text
Source Markdown
-> Parser
-> AST
-> Projector
-> Graph Facts
-> Consumer-defined storage or application use
```

This pipeline MUST remain conceptually clear in both code and documentation.
Any change that weakens that clarity SHOULD be treated with suspicion and justified explicitly.