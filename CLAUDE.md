# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

Project OS is a project/task management app built on **Apiato 13** (Laravel, Porto SAP architecture) with an **Inertia v3 + Vue 3 + TypeScript** frontend (PrimeVue 5, Tailwind 3, Tiptap). PostgreSQL 16, Redis, Passport (OAuth2), spatie/laravel-permission, spatie/laravel-medialibrary, Ziggy for named routes in JS, and LLPhant for the AI agent.

## Commands

Everything runs in Docker. The dev stack is `app` (php-fpm + node), `nginx` (http://localhost:8000), `db` (Postgres on 127.0.0.1:5433), `redis`, `supervisor` (queue workers + scheduler), `mailhog` (UI on :8025).

```bash
make local-up / make local-down          # start/stop dev stack
docker compose exec app php artisan ...  # artisan inside the app container
docker compose exec app npm run dev      # Vite dev server (port 5173)
npm run build                            # production asset build
npm run type-check                       # vue-tsc --noEmit
make migrate                             # php artisan migrate --force
make optimize                            # php artisan optimize:clear
make seed-projects / seed-tasks PROJECT=<id> / seed-comments PROJECT=<id> / seed-media PROJECT=<id>  # dev data, see README.md
php artisan ziggy:generate               # regenerate resources/js/ziggy.js after adding/renaming routes
```

### Tests

Tests run in a separate Docker stack (`docker-compose.testing.yml`, project `project-os-testing`) against a tmpfs Postgres (`db.testing`). `.env.testing` is mounted as the container's `.env` (copy from `.env.testing.example`).

```bash
make testing-up        # start testing stack (first time: make testing-build)
make testing-bash      # shell into app.testing, then:
vendor/bin/phpunit                                   # all suites
vendor/bin/phpunit --testsuite Functional            # suites: Unit, Functional, Ship-Unit
vendor/bin/phpunit --filter testFiltersByPartialTitleMatch
vendor/bin/phpunit app/Containers/AppSection/Project/Tests/Functional/WEB/ListProjectsTest.php
vendor/bin/paratest                                  # parallel run
```

Note: `make testing-down` currently runs `up -d` (Makefile bug) — use `docker compose -f docker-compose.testing.yml -p project-os-testing down` to stop it.

Test discovery (phpunit.xml): `app/Containers/*/*/Tests/{Unit,Functional}`, `app/Ship/Tests/Unit`, `tests/Unit`. Each container has its own `Tests/` base classes (`ContainerTestCase`, `UnitTestCase`, `FunctionalTestCase`, `WebTestCase`/`ApiTestCase`); new tests should extend the container's base and use `#[CoversClass(...)]`.

### Static analysis / style

```bash
composer fixer          # php-cs-fixer (@Symfony + rules in .php-cs-fixer.dist.php), app/ only
vendor/bin/phpstan      # level max, strict rules + larastan + safe-rule; cache in phpstan_cache/
vendor/bin/psalm
```

## Backend architecture (Porto)

- `app/Ship/` — shared kernel: `Parents/*` base classes every container class must extend (`Parents\Actions\Action`, `Parents\Tasks\Task`, `Parents\Controllers\WebController`, `Parents\Requests\Request`, `Parents\Repositories\Repository`, ...), reusable Criteria, `HandleInertiaRequests` middleware (shares `auth.user`), `PaginatorScrollMetadata`, seeders, Ship-level migrations.
- `app/Containers/{Section}/{Container}/` — feature modules. Sections: `AppSection` (Authentication, Authorization, User, Project, Task, Comment, Media, TimeEntry) and `Integration` (AiAgent). Each container's `composer.json` is merged into the root via wikimedia/composer-merge-plugin.
- Apiato auto-discovers per-container `Configs/`, `Data/Migrations/`, `Data/Seeders/`, `Providers/`, and `UI/{WEB,API}/Routes/*.php` — no central registration needed. Config key = file name (e.g. `config('appSection-task.…')`, `config('integration-aiAgent.…')`).

### Request flow

`UI/WEB/Routes/<Name>.v1.private.php` → invokable `UI/WEB/Controllers/<Name>Controller` → `Actions/<Name>Action::run($request)` → one or more `Tasks/*Task::run(...)` → `Data/Repositories/*Repository` (l5-repository).

- Controllers are thin: call an Action, transform with **Fractal transformers that live in `UI/API/Transformers`** even for web pages (`fractal($data, new XTransformer())->parseIncludes([...])->toArray()`), then `Inertia::render('<page-name>', [...])`. Mutating controllers typically redirect back.
- Actions orchestrate and may call Actions/Tasks from other containers (e.g. `ListTasksController` uses `Project\Actions\ListAllProjectsAction`). Tasks do single units of work against a repository.
- Authorization: Request `authorize()` calls `$this->user()->can(...)` against container Policies (`Project/Policies/ProjectPolicy`, `ProjectMemberPolicy` — the latter registered via `Gate::policy` in `ProjectServiceProvider`; both extend `BasePolicy` with owner/admin checks). Transformers expose the same checks to the UI as a permissions object (see `ProjectTransformer`: `update`, `delete`, `manage_members`), so the frontend decides what to show from these flags instead of duplicating the rules.
- Visibility scoping lives in repositories (e.g. `TaskRepository::scopeVisibleForUser` — tasks in projects the user owns/is a member of, or project-less tasks they created/are assigned).

### Filtering & infinite scroll pattern

- List filters use l5-repository request criteria: declare `$fieldSearchable` on the repository (e.g. `'title' => 'ilike'`, `'status' => 'in'`) and call `->addRequestCriteria()` in the Task. The frontend builds l5-repository `search` params (`field:value;field:value`, joined by `searchJoin`) in `resources/js/shared/lib/search-param.ts`, with generic query helpers in `query.ts`.
- Paginated lists are returned with `Inertia::paginatedScroll($transformed, $paginator)` and consumed on the client with Inertia's `<InfiniteScroll>` component (see `pages/tasks/ui/TasksPage.vue`).

### AI agent (Integration/AiAgent)

`SendChatMessageAction` stores the user message plus a `PENDING` assistant message, then dispatches `RunAiAgentJob` (queue) which runs the LLM via LLPhant. Provider is chosen by `LLPHANT_PROVIDER` (openrouter default, openai, gemini) in `Configs/integration-aiAgent.php`. Agent tools extend `Tools/BaseTool` and must be registered in that config's `tools` array.

## Frontend architecture (Feature-Sliced Design)

`resources/js/` with `@` aliased to it. Layers (import only downward): `app` → `pages` → `widgets` → `features` → `entities` → `shared`. Each slice exposes a public API via `index.ts` and has `ui/`, `model/`, `api/` segments as needed.

- Inertia page resolution: `Inertia::render('tasks')` loads `resources/js/pages/tasks/index.ts` (its default export is the page component). Page names in controllers must match `pages/*` directory names.
- PrimeVue components are auto-imported (`unplugin-vue-components`; types in `components.d.ts`) — don't import them manually.
- Named routes use Ziggy's `route()` (`@routes` in `resources/views/app.blade.php`; types in `ziggy.d.ts`).

## OpenSpec workflow

Feature work is spec-driven via OpenSpec (`openspec/`): current specs in `openspec/specs/<capability>/`, completed changes in `openspec/changes/archive/`. Use the `/opsx:*` commands or `openspec-*` skills (propose → apply → archive/sync) when asked to plan or implement a change.
