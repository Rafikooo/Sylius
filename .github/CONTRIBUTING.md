Contributing Patches and BDD Methodology
========================================

Sylius is a Open Source project driven by the community. 
Join our amazing adventure and we promise to be nice and welcoming to everyone. 
Remember, you do not have to be a Symfony guru or even a programmer to help!

You can learn [how to contribute the patches](https://docs.sylius.com/en/latest/contributing/code/patches.html)
in our [Contributing Guide](https://docs.sylius.com/en/latest/contributing/index.html).

Translations
------------

If you find a typo in English, please follow the contribution guidelines as specified above.  
If you find a typo or a missing translation in another language than English: [go to Crowdin](https://crowdin.com/project/sylius) and suggest your changes!

We really appreciate your help, thank you so much for making Sylius international!

See [the documentation to learn more about how to contribute to our translations](https://docs.sylius.com/en/latest/book/contributing/translations/index.html).

Security Issues
---------------

We treat security very seriously, you can read about security procedures [here](https://docs.sylius.com/en/latest/contributing/code/security.html).

Continuous Integration
-----------------------

Our CI is organized for clarity and reuse:

- Entry workflows
    - `ci_pr.yaml` – runs on pull_request and push. It executes static checks, packages, frontend and E2E smoke across selected PHP/Symfony/DB combos. Job names are prefixed with the target branch, for example: `[2.1] E2E (MySQL)`.
    - `ci_cron.yaml` – nightly builds (schedule) with full matrices for branches: `1.14`, `2.0`, `2.1`, `2.2`, plus an `unstable` suite. Although the schedule runs from the default branch only, each job passes the target branch explicitly so you will see `[1.14]`, `[2.0]`, etc. in job names.

- Reusable workflows (no triggers)
    - `ci_static-checks.yaml` – static analysis for a single `php`/`symfony` pair.
    - `ci_e2e.yaml` – E2E for a single combination with inputs: `db` (`mysql|mariadb|postgresql`), `db_version`, optional `twig` and `state_machine_adapter`, `unstable` flag.
    - `ci_packages.yaml` – packages tests for a single `php`/`symfony` (and optional `orm`). Supports `unstable` and `ignore_failure` flags.
    - `ci_frontend.yaml` – frontend checks for a single Node.js version.

Forks and PRs

- Opening a PR from a fork triggers `CI (PR/Push)` on the upstream repository. Secrets are not available to PRs from forks, but our PHP/Behat/JS tests do not require them.
- You can also run the same workflows on your fork for quicker iteration. Both `ci_pr.yaml` and `ci_cron.yaml` expose a manual “Run workflow” button (workflow_dispatch).

Reading job names

- For PRs, job names include the target branch in brackets (for example, `[1.14] Static checks`).
- For nightly runs, job names are grouped by target branch the job validates.
