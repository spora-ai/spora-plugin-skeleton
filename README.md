# Spora Plugin Skeleton

Skeleton for a [Spora](https://github.com/spora-ai/spora-core) plugin.

Use this repository as a template for any new `spora-plugin`:

1. Click **Use this template** → **Create a new repository** on GitHub.
2. Rename the package in `composer.json` (e.g. `spora-ai/spora-plugin-tavily`).
3. Rename the namespace (`Spora\Plugins\Skeleton` → `Spora\Plugins\<YourPlugin>`)
   in every PHP file.
4. Update `plugin.json`'s `slug`, `description`, `class`, and `icon`.
5. Replace `src/Tools/EchoTool.php` with your real tool(s); add more files
   under `src/Tools/` and list them in `src/SkeletonPlugin.php::tools()`.
6. If your plugin needs database tables, add Laravel migrations under
   `database/migrations/` and bump `SkeletonPlugin::schemaVersion()`.
7. If your plugin ships operator-facing strings, keep `locales/de.json` and
   `translationsPaths()`, and add a German entry for each new string. A copy
   edit is a different case: the key *is* the English text, so the old key
   has to be **replaced**, not kept — an entry for text the source no longer
   produces is an orphan that nothing will ever resolve. See
   [Translations](#translations) below for what belongs there and what does
   not — the rule is narrower than it looks.

## Translations

A plugin ships its own catalogue at `locales/<lang>.json`, declared through
`SkeletonPlugin::translationsPaths()`. Keys are the **English source text**:

```json
{ "How the greeting is phrased.": "Wie die Begrüßung formuliert ist." }
```

That is deliberate. Because the key *is* the string, editing the English
orphans the translation: the old entry matches nothing and the new English
has no entry, so the string renders in English while the stale entry sits in
the file looking perfectly fine. Semantic keys would make every copy edit a
silent no-op.

**The host does not check your catalogue, so the orphan is inert.**
`i18n:check` is the framework's own gate: it harvests the framework's own
English sources and compares them with the framework's own catalogues. It
has no notion of a plugin's catalogue — run against a plugin repository it
compares that repository's `de.json` against the framework's strings and
reports every catalogue key as an orphan. Nothing fails until the plugin's
own CI checks it, which is why the skeleton ships the check:
`tests/Unit/LocaleCatalogTest.php` asserts that the catalogue
directory is the one `translationsPaths()` declares, that the JSON is valid,
that no value is empty or identical to its English key, and that no key
survives a copy edit. Copy it into your plugin. The Spora docs describe the
same check under
[Translations → Checking your catalogue](https://docs.spora-ai.com/develop/plugins/author-guide/translations#checking-your-catalogue).

Spora resolves a string through **the host's** catalogue first, then yours,
then the English literal. Shipping no catalogue at all is fine; you render
English until you add one. On a collision — the same English key in both
catalogues — **the host wins: your entry is dropped, with a warning in the
log.** A plugin can only add a key, never shadow one, so a string you share
with the host is translated in the host's catalogue alone.

### What not to translate

**If the model reads it, it stays English — and so does the operator's view of
it.** A German tool description degrades the model's tool selection. A
translation is not scoped to one use either: because the catalogue key *is*
the English text, it applies to every place that text appears — which is
why the label and options rows below are unconditional.

| Do not translate | Because |
|---|---|
| `#[Tool].description` | goes into the LLM function definition |
| `#[ToolParameter].description` | goes into the LLM parameter schema |
| `#[ToolSetting].label` | **never** translated, whatever the setting declares. *Some* settings carry `exposeToLlm: true` and enter the model's config block, so a translated label would put German into the prompt the moment somebody adds the flag — and because a catalogue key *is* the English text, it would apply to every setting reusing the same word |
| `#[ToolSetting].options` | the **labels** are language endonyms that must read verbatim (`Deutsch` stays `Deutsch`), and the **keys** (`'formal'`, `'casual'`) are the stored values a model or an API consumer matches on. Neither half of the map goes into the catalogue |
| `ToolResult::ok()` / `fail()` content | model input |
| `#[ToolOperation].operatorDescription` | reaches the model via the agent manifest |

`#[ToolSetting].description` **is** operator copy and does translate — see the
annotated example in `CompanionTool`.

## Authoring guidelines

Framework-level conventions — which classes are plugin-stable, what's
framework-internal, schema versioning, deprecation policy — live in the
[Spora docs → Plugin system](https://docs.spora-ai.com/reference/concepts/plugins-system).
The driver / history value-object layer is **framework-internal**:
route plugin logic through `AgentOrchestrator` and `TaskService`.

> **Skills feature note.** The `skillPaths()` hook needs `spora-core ≥ 0.12`.
> The skeleton's `composer.json` floors core at `>=0.30.0`, so the hook is
> always there; an older core simply never calls the method.

## Layout

```
.
├── composer.json          # name=spora-ai/spora-plugin-<x>, type=spora-plugin
├── plugin.json            # manifest the PluginLoader reads at boot
├── locales/               # translation catalogues (one <lang>.json per language)
│   └── de.json            # the German catalogue; keys are the English source text
├── src/
│   ├── SkeletonPlugin.php # PluginInterface implementation (FQCN matches plugin.json `class`)
│   ├── CompanionTool.php  # the tool + bundled-skill example
│   └── Tools/
│       └── EchoTool.php   # one tool per file (replace this one)
├── skills/                # skills shipped with the plugin (one folder per skill)
├── agent-templates/       # agent-template files shipped with the plugin
├── tests/                 # Pest unit tests
│   ├── Pest.php
│   └── Unit/
└── .github/workflows/
    └── ci.yml             # pest + phpstan + cs-fixer
```

`skills/` and `agent-templates/` are present in the template (with
`.gitkeep`) so the directory references in `SkeletonPlugin::skillPaths()`
and `SkeletonPlugin::agentTemplatePaths()` resolve out of the box.
Plugin authors can leave them empty if the plugin ships none, or delete
the methods in `SkeletonPlugin.php` to drop the hook entirely.

## Bundled skills (recommended pattern)

`src/CompanionTool.php` declares `recommendsSkills: ['companion-skill']`
on its `#[Tool]` attribute, and `skills/companion-skill/SKILL.md` ships
the matching skill directory. Together they are the fork-and-go example
of spora-core's recommended-skill contract:

- The attribute bundles the skill for the operator — agents can pick it
  from the Skill tool's `allowed_skills` multi-select without any extra
  wiring on your part.
- The runtime validator in spora-core ≥ 0.29.0 enforces strict mode:
  if any slug in `recommendsSkills` is not on disk, `GET /api/v1/tools`
  returns HTTP 500 with code `TOOLS_RECOMMENDS_SKILLS_MISSING` for the
  whole plugin — not just the offending tool. Ship the directory or
  remove the slug; the attribute cannot stay half-declared.
- `tests/Unit/CompanionToolValidationTest.php` is the recommended
  plugin-author pattern: build a `ToolConfigNameResolver` seeded with
  your tool class, hand it to a `ToolsRecommendsSkillsValidator`
  alongside a synthesised `SkillScanner`, and assert `validate()` returns
  an empty list. Copy that test verbatim — it's the build-time gate
  that catches a missing bundled skill before it reaches the tools page.

Delete `CompanionTool`, its companion skill, and the two tests when you
fork the repo into a real plugin; keep `skillPaths()` wired up if your
plugin ships any skills of its own.

## Local development

Clone the repo, install dependencies, and run the tests:

```bash
composer install
./vendor/bin/pest
```

## Publishing

1. Tag the release: `git tag v0.1.0 && git push --tags`.
2. (Optional) Configure Packagist to auto-pull from the GitHub repo.

There's nothing to bump in `plugin.json` or `composer.json` — the runtime reads the version from the git tag via `Composer\InstalledVersions::getPrettyVersion()`, so the tag is the single source of truth.

## CI

Three parallel jobs run on every push to `main`, on `v*` tags, and on
pull requests:

- `test` — Pest on PHP 8.4 + 8.5
- `static-analysis` — PHPStan level 5
- `code-style` — php-cs-fixer dry-run (same ruleset as Spora core)

External actions are pinned to full commit SHAs per the project's supply-chain
policy.
