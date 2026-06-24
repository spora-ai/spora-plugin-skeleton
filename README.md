# Spora Plugin Skeleton

Skeleton for a [Spora](https://github.com/spora-ai/spora-core) plugin.

Use this repository as a template for any new `spora-plugin`:

1. Click **Use this template** → **Create a new repository** on GitHub.
2. Rename the package in `composer.json` (e.g. `spora-ai/spora-plugin-tavily`).
3. Rename the namespace (`Spora\Plugins\Skeleton` → `Spora\Plugins\<YourPlugin>`)
   in every PHP file.
4. Update `plugin.json`'s `slug`, `description`, `class`, and `icon`.
5. Replace `src/Tools/EchoTool.php` with your real tool(s); add more files
   under `src/Tools/` and list them in `src/Plugin.php::tools()`.
6. If your plugin needs database tables, add Laravel migrations under
   `database/migrations/` and bump `SkeletonPlugin::schemaVersion()`.

## Layout

```
.
├── composer.json          # name=spora-ai/spora-plugin-<x>, type=spora-plugin
├── plugin.json            # manifest the PluginLoader reads at boot
├── src/
│   ├── Plugin.php         # PluginInterface implementation
│   └── Tools/
│       └── EchoTool.php   # one tool per file (replace this one)
├── tests/                 # Pest unit tests
│   ├── Pest.php
│   └── Unit/
└── .github/workflows/
    └── ci.yml             # pest + phpstan + cs-fixer
```

## Local development

The skeleton's `composer.json` declares a path repository pointing at
`../spora-core`. Clone Spora alongside this plugin so the dependency
resolves:

```bash
git clone https://github.com/spora-ai/spora-core.git ../spora-core
composer install
./vendor/bin/pest
```

Remove the `repositories` block from `composer.json` once
[`spora/core`](https://packagist.org/packages/spora/core) ships on Packagist.

## Publishing

1. Bump `version` in `composer.json`.
2. Tag the release: `git tag v0.1.0 && git push --tags`.
3. (Optional) Configure Packagist to auto-pull from the GitHub repo.

## CI

Three parallel jobs run on every push to `main`, on `v*` tags, and on
pull requests:

- `test` — Pest on PHP 8.4 + 8.5
- `static-analysis` — PHPStan level 5
- `code-style` — php-cs-fixer dry-run (same ruleset as Spora core)

External actions are pinned to full commit SHAs per the project's supply-chain
policy.
