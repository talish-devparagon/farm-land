# Farmland Implementation Plan

This document splits the farmland feature build into 4 independent phases (A–D) plus a final Verification pass. Each phase can be picked up by a separate agent. Read the **Shared Contract** section first — it's binding for everyone, regardless of which phase you're assigned.

---

## How to use this document

- Pick **one** phase (A, B, C, or D). Do not edit files owned by another phase.
- The **Shared Contract** section below is frozen — model names, enum values, DB schema, and Livewire component public props/methods will not change. Code against it even if the phase that "owns" that piece hasn't merged yet.
- If your phase's tests fail only because of `SQLSTATE... no such table` errors, that means Phase A hasn't merged yet — not that your code is wrong. Write and commit your tests anyway; they'll go green once A lands.
- Run these three commands before declaring your phase done, every time:
  ```
  vendor/bin/pint --dirty --format agent
  vendor/bin/phpstan analyse
  php artisan test --compact
  ```
- Use Boost MCP's `search-docs` tool before touching any Laravel/Livewire/Flux API you're not 100% sure of. This is mandated by the project's own CLAUDE.md.
- Activate the relevant Claude Code skill for your domain as soon as you start working in it: `fortify-development`, `laravel-best-practices`, `fluxui-development`, `livewire-development`, `pest-testing`, `tailwindcss-development`.
- Do **not** add or change composer/npm dependencies without approval. The stack is fixed: Flux **free tier only** (no Pro), no Volt, Pest (not PHPUnit-style).

---

## Dependency graph

```
Phase A (Data Layer)
   │
   ├──> Phase B (Livewire Components, Routes, Validation)
   ├──> Phase C (Authorization, Actions, Registration)
   └──> Phase D (Frontend / UI) ── can start coding immediately,
                                    contract is frozen, doesn't
                                    need A/B/C's actual code

   All four ──> Verification (run once A, B, C, D are all merged)
```

**Practical note:** B, C, and D can all start writing code in parallel *right now*, even before A merges, because the contract below is frozen. Their test suites just won't pass until A's migrations/models exist. Phase A should be claimed and finished as fast as possible since every other phase's tests depend on it.

---

## Verified stack facts (apply to all phases)

These were confirmed by reading the project's `vendor` source directly — don't re-derive them, just use them:

- **Routing a full-page Livewire component**: use `Route::livewire($uri, Component::class)->name(...)`. This is the existing convention in `routes/settings.php` — follow it.
- **Validation pattern**: this codebase uses a **rules-as-trait** convention (see `app/Livewire/Settings/Profile.php`, `app/Concerns/ProfileValidationRules.php`): a `protected function xRules(): array` method on a `Concerns` trait, mixed into the Livewire component, called via `$this->validate($this->xRules())`. Follow this for every new form — don't use `#[Validate]` attributes instead.
- **Flux free tier — available**: `button`, `input` (incl. `type="date"`), `select`, `textarea`, `checkbox`, `radio`, `switch`, `modal`, `dropdown`, `menu`, `table` (sortable), `badge`, `toast`, `pagination`, `navlist`, `navbar`, `sidebar`, `avatar`, `heading`, `text`, `card`, `callout`, `separator`, `tooltip`, `breadcrumbs`.
- **Flux free tier — NOT available, do not use**: `flux:tabs`, `flux:date-picker`, `flux:calendar`, `flux:chart`, `flux:editor` (all require Flux Pro).
    - Tabs → use `flux:button.group` or `flux:navlist` bound to a Livewire property, toggling panels with `x-show`/`@if`.
    - Dates → `<flux:input type="date" ... />`.
- **⚠️ Pest is currently broken**: `tests/Pest.php` has `RefreshDatabase` commented out, so every DB test fails with `no such table: users`. **Phase A must fix this first** — it blocks every other phase's tests.
- **Pest style**: function-based `test('description', function () { ... })`, not `it()`. Use `Livewire::test(Component::class)`, `expect($x)->toBe(...)`. Reference: `tests/Feature/Settings/ProfileUpdateTest.php`.
- **Larastan level 7**, no baseline. Type-hint every public Livewire property explicitly.

---

## Shared Contract

### Enums — `app/Enums/*.php`, all `: string` backed, `TitleCase` case names

| Enum | Cases (Name → value) |
|---|---|
| `AnimalSex` | `Male` → `male`, `Female` → `female` |
| `AnimalStatus` | `Alive` → `alive`, `Sold` → `sold`, `Deceased` → `deceased` |
| `FarmStatus` | `Active` → `active`, `Suspended` → `suspended` |
| `HealthRecordType` | `Vaccination` → `vaccination`, `Treatment` → `treatment`, `VetVisit` → `vet_visit` |
| `UserRole` | `Admin` → `admin`, `FarmOwner` → `farm_owner` |

### Database schema (migration order matters)

| Table | Key columns | Notes |
|---|---|---|
| `farms` | `owner_id` FK→`users.id` cascadeOnDelete; `name` string; `location` string nullable; `phone` string nullable; `status` string default `'active'` indexed; timestamps; **softDeletes** | |
| `users` (alter, own migration, **after** `farms`) | add `role` string default `'farm_owner'` indexed (after `password`); add `farm_id` FK→`farms.id` nullable, nullOnDelete (after `role`) | Must come after farms table for FK to resolve |
| `animals` | `farm_id` FK→`farms.id` cascadeOnDelete; `tag_number` string; `name`/`breed` string nullable; `sex` string; `date_of_birth` date nullable; `mother_id`/`father_id` FK→`animals.id` nullable, nullOnDelete; `status` string default `'alive'` indexed; `current_weight` decimal(8,2) nullable; `notes` text nullable; timestamps; **softDeletes**; **unique(`farm_id`,`tag_number`)** | |
| `weight_logs` | `animal_id` FK→`animals.id` cascadeOnDelete; `weight` decimal(8,2); `recorded_at` date indexed; timestamps | no soft deletes |
| `health_records` | `farm_id` FK→`farms.id` cascadeOnDelete; `animal_id` FK→`animals.id` cascadeOnDelete; `type` string; `description` text; `date` date; `next_due_date` date nullable indexed; `notes` text nullable; timestamps | no soft deletes |
| `breeding_records` | `farm_id` FK→`farms.id` cascadeOnDelete; `doe_id` FK→`animals.id` cascadeOnDelete; `buck_id` FK→`animals.id` nullable, nullOnDelete; `mating_date` date; `expected_kidding_date` date indexed; `actual_kidding_date` date nullable; `number_of_offspring` unsignedInteger nullable; `notes` text nullable; timestamps | no soft deletes |

### Models & relationships (mirror cattle-farm exactly)

- **`Farm`**: `HasFactory`, `SoftDeletes`; default `status = FarmStatus::Active`; `owner(): belongsTo(User, 'owner_id')`; `animals(): hasMany(Animal)`.
- **`Animal`**: `BelongsToFarm`, `HasFactory`, `SoftDeletes`; default `status = AnimalStatus::Alive`; casts `sex`→`AnimalSex`, `status`→`AnimalStatus`, `date_of_birth`→`date`, `current_weight`→`decimal:2`; relations `farm()`, `mother()`/`father()` (self belongsTo), `offspringAsMother()`/`offspringAsFather()` (self hasMany), `weightLogs()`, `healthRecords()`, `breedingRecordsAsDoe()`/`breedingRecordsAsBuck()`; helper `offspring(): Collection` merging both offspring relations sorted by DOB desc.
- **`WeightLog`**: `HasFactory` only (no soft deletes, no `BelongsToFarm` — scoped indirectly via `animal→farm`); casts `weight`→`decimal:2`, `recorded_at`→`date`; `animal()` belongsTo.
- **`HealthRecord`**: `BelongsToFarm`, `HasFactory`; casts `type`→`HealthRecordType`, `date`/`next_due_date`→`date`; `farm()`, `animal()`.
- **`BreedingRecord`**: `BelongsToFarm`, `HasFactory`; casts 3 date fields; `farm()`, `doe()`/`buck()` belongsTo `Animal`.
- **`User`** (edit existing file — do not remove `TwoFactorAuthenticatable`/`initials()`): add `role` to `#[Fillable]`; add `role`→`UserRole` cast; add `farm(): hasOne(Farm, 'owner_id')`; add `isAdmin(): bool` / `isFarmOwner(): bool` helpers. `farm_id` stays **out of** `#[Fillable]` (set programmatically).
- **Multi-tenancy pattern** — copy verbatim from cattle-farm: `app/Models/Concerns/BelongsToFarm.php` (boots a `FarmScope` global scope; on `creating`, auto-sets `farm_id` from `Auth::user()->farm->id` if not admin and not already set) + `app/Models/Scopes/FarmScope.php` (constrains queries to `farm_id = $user->farm->id` unless admin).

### Livewire component contract

Portion D binds to these public props/methods and **never edits these `.php` files**. Portion B owns these files and must not change these names without updating this contract.

| Component (`app/Livewire/...`) | Route | Key public state | Key methods |
|---|---|---|---|
| `Animals\AnimalsIndex` | `Route::livewire('/animals', ...)->name('animals.index')` | `#[Url] $search`, `#[Url] $breed`, `#[Url] $status`; `WithPagination` trait | `#[Computed] animals()` (filtered/paginated), `#[Computed] breeds()` |
| `Animals\AnimalForm` | `/animals/create` → `animals.create`; `/animals/{animal}/edit` → `animals.edit` (same component, `mount(?Animal $animal = null)`) | fields mirroring `Animal` fillable (`tag_number`, `name`, `breed`, `sex`, `date_of_birth`, `mother_id`, `father_id`, `status`, `current_weight`, `notes`) + `public ?Animal $animal` | `save()` (create-or-update, redirects to `animals.show`), `#[Computed] candidateParents()` |
| `Animals\AnimalShow` | `/animals/{animal}` → `animals.show`, `mount(Animal $animal)` | `public string $activeTab = 'health'` (`health\|breeding\|offspring`); weight-log inline fields `$weight`, `$recorded_at` | `logWeight()`, `delete()`, `openHealthRecordModal(?int $id = null)`, `openBreedingRecordModal(?int $id = null)`; `#[Computed]` for `healthRecords`, `breedingRecords`, `weightLogs`, `offspring`, `mother`, `father` |
| `Animals\HealthRecordFormModal` | not routed — rendered inline inside `AnimalShow`'s view: `<livewire:animals.health-record-form-modal :animal="$this->animal" />` | `public Animal $animal`; `public bool $show = false`; fields mirroring `HealthRecord` fillable minus `farm_id`/`animal_id` | `#[On('open-health-record-modal')] open(?int $healthRecordId = null)`, `save()` (dispatches `health-record-saved`), `delete()` |
| `Animals\BreedingRecordFormModal` | same pattern as above | `public Animal $animal` (the doe); fields mirroring `BreedingRecord` fillable minus `farm_id`/`doe_id`; plus `create_offspring: bool`, `offspring: array<{tag_number,name,sex}>` when `actual_kidding_date` is set | `#[On('open-breeding-record-modal')] open(?int $id = null)`, `save()` (calls `CreateOffspringAnimalsAction` when offspring recorded, dispatches `breeding-record-saved`), `delete()` |
| `Upcoming` | `/upcoming` → `upcoming.index` | — | `#[Computed] upcomingHealthRecords()`, `#[Computed] upcomingBreedingRecords()` — both delegate to `GetUpcomingRemindersAction`, never inline the query |

- Parent↔child modal communication uses **Livewire events** (`$this->dispatch('open-health-record-modal', healthRecordId: $id)` / `#[On(...)]`), not reactive props.
- All routes above sit behind `Route::middleware(['auth', 'verified'])->group(...)` in `routes/web.php`, same as the existing `dashboard` route.

---

## Known pitfalls to avoid (from cattle-farm — bake these fixes in from day one)

1. **Orphaned reminders after soft-delete.** `HealthRecord`/`BreedingRecord` don't get cleaned up when their `Animal` is soft-deleted (`cascadeOnDelete()` never fires on soft-delete). Fix: bake `whereHas('animal')` / `whereHas('doe')` into the upcoming-reminders query from the start (Phase C, `GetUpcomingRemindersAction`).
2. **Business logic leaking into controllers/components.** Don't recompute `current_weight` inline in a Livewire method. That logic belongs in `RecalculateAnimalCurrentWeightAction` (Phase C), called from `AnimalShow::logWeight()` (Phase B) — never inlined.
3. **No Farm on registration.** A freshly registered user has no `Farm`/`farm_id`, so `AnimalPolicy::create()` blocks them from creating anything. Fix: Phase C must make `CreateNewUser` also create a `Farm`, transactionally.
4. **Tag-number uniqueness is per-farm, not global.** The `animals` unique constraint is `['farm_id', 'tag_number']`. Validation rules (Phase B) must scope `unique`/`exists` checks by the current user's `farm_id` — never a plain global-unique rule.

---

## Phase A — Data Layer

**Status: claim this first.** Every other phase's tests are blocked until this merges.
**Depends on:** nothing.

**Owns:**
`database/migrations/*_{create_farms_table,add_role_and_farm_id_to_users_table,create_animals_table,create_weight_logs_table,create_health_records_table,create_breeding_records_table}.php`, `app/Enums/*.php`, `app/Models/{Farm,Animal,WeightLog,HealthRecord,BreedingRecord}.php` (new), `app/Models/User.php` (edit), `app/Models/Concerns/BelongsToFarm.php`, `app/Models/Scopes/FarmScope.php`, `database/factories/{Farm,Animal,WeightLog,HealthRecord,BreedingRecord}Factory.php`, `database/factories/UserFactory.php` (edit), `database/seeders/*`, `tests/Pest.php` (edit).

**Tasks:**
1. In `tests/Pest.php`, uncomment `->use(RefreshDatabase::class)`. Verify with `php artisan test --filter=ExampleTest` that it now passes.
2. Create the 6 migrations per the schema table above, using `php artisan make:migration --no-interaction`.
3. Create the 5 enums per the table above.
4. Create the 5 new models + edit `User` per the "Models & relationships" section. Copy `BelongsToFarm`/`FarmScope` verbatim from cattle-farm.
5. Create factories. Port cattle-farm's special states:
    - `HealthRecordFactory::dueSoon()` — `next_due_date` within 30 days.
    - `BreedingRecordFactory::completed()` — `actual_kidding_date` ~145–155 days after mating + `number_of_offspring` 1–3.
    - Edit `UserFactory`: add `role` to `definition()` (default `UserRole::FarmOwner`), add an `admin()` state.
6. Create seeders mirroring cattle-farm's order:
    - `DatabaseSeeder` → one admin user + one farm-owner user + their `Farm` (with `farm_id` back-filled)
    - `FarmSeeder` (5 more farms)
    - `AnimalSeeder` (8–15 animals/farm)
    - `WeightLogSeeder` (3–6 logs/animal, then set `current_weight` to the latest)
    - `HealthRecordSeeder` (1–3 records/animal, 40% chance of an extra `dueSoon()` one)
    - `BreedingRecordSeeder` (up to 3 does/farm get a record, 50% chance `completed()`)
    - Use `WithoutModelEvents` in every seeder (bypasses `BelongsToFarm::creating` auto-scoping — set `farm_id` explicitly via factory `for()`).
7. Write Pest tests: model casts/relationships resolve correctly; `FarmScope` filters a non-admin's queries to their own farm and does not filter an admin's; `BelongsToFarm::creating` auto-assigns `farm_id`; factory states produce the documented data shape.
8. Run `pint --dirty --format agent`, `phpstan analyse`, `test --compact`.

---

## Phase B — Livewire Components, Routes & Validation

**Depends on:** Phase A's model/enum names (code against the contract now; tests need A merged to actually run).

**Owns:**
`app/Livewire/Animals/{AnimalsIndex,AnimalForm,AnimalShow,HealthRecordFormModal,BreedingRecordFormModal}.php`, `app/Livewire/Upcoming.php`, `app/Concerns/{Animal,HealthRecord,BreedingRecord}ValidationRules.php`, `routes/web.php` (**additions only** — don't touch existing `/`, `/dashboard`, `require settings.php` lines), Pest feature tests for all of the above.

**Tasks:**
1. Before writing routes, use Boost's `search-docs` MCP tool to confirm route-model-binding into a full-page Livewire component's `mount()` still works as expected in Livewire 4.
2. Build each Livewire component per the contract table above. Port validation logic from cattle-farm's Form Requests into the `*ValidationRules` traits:
    - **`AnimalValidationRules`**: `tag_number` required + unique **scoped to the current user's `farm_id`** (ignore self on update); `sex`/`status` required enum; `date_of_birth` nullable date before_or_equal today; `mother_id`/`father_id` nullable, must exist within the same farm, and (on edit) must not equal the animal's own id; `current_weight` nullable numeric min 0.
    - **`HealthRecordValidationRules`**: `type` required enum; `description` required; `date` required date before_or_equal today; `next_due_date` nullable date after `date`.
    - **`BreedingRecordValidationRules`**: `buck_id` nullable, must exist within the farm and be `sex = male`; `mating_date` required before_or_equal today; `expected_kidding_date` required after `mating_date`; `actual_kidding_date` nullable after_or_equal `mating_date`; offspring sub-fields required_with `offspring`, `tag_number` distinct + unique scoped to farm.
    - Weight-log inline fields on `AnimalShow`: `weight` required numeric min 0; `recorded_at` required date before_or_equal today.
3. Authorize every mutating action via `Gate::authorize(...)` against `Animal` (using ability names `viewAny`/`view`/`create`/`update`/`delete` — Phase C's policy doesn't need to exist yet to write these calls).
4. `AnimalShow::logWeight()` and the breeding-record modal's offspring flow **must** call Phase C's `RecalculateAnimalCurrentWeightAction` / `CreateOffspringAnimalsAction` — do not inline that logic (pitfall #2).
5. `Upcoming`'s computed properties **must** call Phase C's `GetUpcomingRemindersAction` — do not inline `whereBetween`/`whereHas` logic (pitfall #1).
6. Each component's `render()` must return a specific, predictable view name matching Livewire's kebab-case convention (e.g. `AnimalsIndex` → `livewire.animals.animals-index`). Create a **minimal placeholder** Blade file at that path (bare HTML with correct `wire:model`/`wire:click`/`wire:submit` attribute names so your own tests pass) — Phase D will replace its contents, not its filename.
7. Write Pest feature tests using `Livewire::test(Component::class)` for: happy-path create/update/delete for each entity; farm-scoping (a user cannot fetch/mutate another farm's records — expect 404 or authorization failure); validation failures (`assertHasErrors`); the weight-log "latest by `recorded_at`, not by insertion order" regression test (port cattle-farm's exact test); an "upcoming excludes soft-deleted animal's records" regression test (port cattle-farm's exact test).
8. Run `pint --dirty --format agent`, `phpstan analyse`, `test --compact`.

---

## Phase C — Authorization, Actions & Registration

**Depends on:** Phase A's model names (code against the contract now; tests need A merged to actually run).

**Owns:**
`app/Policies/AnimalPolicy.php`, `app/Actions/CreateOffspringAnimalsAction.php`, `app/Actions/RecalculateAnimalCurrentWeightAction.php`, `app/Actions/GetUpcomingRemindersAction.php`, `app/Actions/Fortify/CreateNewUser.php` (edit existing), Pest tests for all of the above.

**Tasks:**
1. **`AnimalPolicy`**: port cattle-farm's 7 methods verbatim (`viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`) — all farm-owner + same-farm checks, `forceDelete` always `false`. Rely on Laravel's policy auto-discovery (`Animal` → `AnimalPolicy`) — no explicit `Gate::policy()` registration needed. `HealthRecord`/`BreedingRecord` continue to be authorized indirectly through the parent `Animal`'s `update` ability — no separate policies for those two.
2. **`CreateOffspringAnimalsAction::handle(BreedingRecord $breedingRecord, array $offspring): void`** — port verbatim from cattle-farm: for each offspring, create an `Animal` with `tag_number`, `name`, `sex` from the array, `date_of_birth = $breedingRecord->actual_kidding_date`, `mother_id = $breedingRecord->doe_id`, `father_id = $breedingRecord->buck_id`, `status = AnimalStatus::Alive`.
3. **`RecalculateAnimalCurrentWeightAction::handle(Animal $animal): void`** — new, extracted from cattle-farm's inline controller logic (pitfall #2): fetch `$animal->weightLogs()->latest('recorded_at')->value('weight')` and update `$animal->current_weight`. Called by Phase B after creating a `WeightLog`.
4. **`GetUpcomingRemindersAction::handle(): array{healthRecords: Collection, breedingRecords: Collection}`** — new, ports cattle-farm's `UpcomingController` query with the fix baked in (pitfall #1):
    - `HealthRecord::with('animal')->whereHas('animal')->whereBetween('next_due_date', [today, today+30])->orderBy('next_due_date')->get()`
    - `BreedingRecord::with('doe')->whereHas('doe')->whereNull('actual_kidding_date')->whereBetween('expected_kidding_date', [today, today+30])->orderBy('expected_kidding_date')->get()`
5. **Edit `app/Actions/Fortify/CreateNewUser.php`** (fixes pitfall #3): after existing validation, wrap in `DB::transaction()`: create the `User` (unchanged validation logic), then create a `Farm` with `owner_id = $user->id` and a default `name` (e.g. `"{$user->name}'s Farm"`), then update the user's `farm_id`. Return the user. Do not remove or alter existing `PasswordValidationRules`/`ProfileValidationRules` trait usage.
6. Write Pest tests: `AnimalPolicy` unit tests for every ability × same-farm/other-farm/admin combination; `CreateOffspringAnimalsAction` test (mirrors cattle-farm's "recording a kidding creates offspring" test); `RecalculateAnimalCurrentWeightAction` test proving it picks the row with the latest `recorded_at`, not the most-recently-inserted row; `GetUpcomingRemindersAction` tests for 30-day window boundaries **and** a regression test proving a soft-deleted animal's records are excluded (reproduce pitfall #1, confirm fixed); a registration test proving a new user ends up with a non-null `farm_id` and an owned `Farm` (reproduce pitfall #3, confirm fixed).
7. Run `pint --dirty --format agent`, `phpstan analyse`, `test --compact`.

---

## Phase D — Frontend / UI (Flux)

**Depends on:** the frozen component contract only. Does **not** need Phase B's actual code to exist — just the public prop/method names above. **Never edit `app/Livewire/**/*.php` files.**

**Owns:**
`resources/views/livewire/animals/{animals-index,animal-form,animal-show,health-record-form-modal,breeding-record-form-modal}.blade.php`, `resources/views/livewire/upcoming.blade.php`, `resources/views/layouts/app/sidebar.blade.php` (edit — nav additions only), any new `resources/views/components/*` you introduce for shared UI (e.g. a status badge).

**Tasks:**
1. Add two nav items to the existing `flux:sidebar.group :heading="__('Platform')"` block in `sidebar.blade.php`, alongside the existing Dashboard item: one for `animals.index` (label "Animals") and one for `upcoming.index` (label "Upcoming"). Use `flux:sidebar.item icon="..." :href="route(...)" :current="request()->routeIs('animals.*')" wire:navigate`. Pick reasonable Heroicons names.
2. **`animals-index.blade.php`**: `flux:table` with sortable columns for tag number/name/status, `flux:input` search bound to `wire:model.live`, `flux:select` for breed/status filters, `flux:badge` for status (color-coded per `AnimalStatus`), Flux pagination for `$this->animals`, a `flux:button` "Add animal" linking to `animals.create` via `wire:navigate`.
3. **`animal-form.blade.php`**: `flux:field` + `flux:input`/`flux:select` per property in the contract, `flux:input type="date"` for `date_of_birth` (no date-picker available), `flux:select` populated from `#[Computed] candidateParents()` for mother/father, `flux:button` submit calling `wire:submit="save"`.
4. **`animal-show.blade.php`**: detail panel (`flux:heading`/`flux:text`/`flux:card`), a tab-button-group bound to `$activeTab` (no `flux:tabs` — use `flux:button.group` or `flux:navlist` items with `@click="$wire.activeTab = '...'"` / `wire:click`) switching between health/breeding/offspring panels, `flux:table` for weight logs with an inline `flux:input` + `flux:button` mini-form for `logWeight()`, "Edit"/"Delete" `flux:button`s (Delete wrapped in a `flux:modal` confirm dialog — Flux's own modal, not a custom Alpine store), and mount both modal components: `<livewire:animals.health-record-form-modal :animal="$animal" />` / `<livewire:animals.breeding-record-form-modal :animal="$animal" />`.
5. **`health-record-form-modal.blade.php`** / **`breeding-record-form-modal.blade.php`**: `flux:modal` wrapping a `flux:field`/`flux:input`/`flux:select` form per the contract's field list; breeding modal additionally shows offspring repeater fields only when `actual_kidding_date` is filled and `create_offspring` is checked.
6. **`upcoming.blade.php`**: two `flux:table`s (or `flux:card` sections) for `upcomingHealthRecords` / `upcomingBreedingRecords`, each row linking to `animals.show` via `wire:navigate`, with a `flux:badge`/empty-state message when a list is empty (mirror cattle-farm's "Nothing due in the next 30 days." copy).
7. Use Flux's built-in variant props (`flux:badge color="..."`) consistently instead of hand-picked Tailwind classes.
8. Write light Pest coverage: `Livewire::test(Component::class)->assertSee(...)` for a few key labels/values per component to catch accidental breakage from markup changes (not exhaustive — Phase B already covers state/behavior).
9. Run `pint --dirty --format agent` (for any `.php` you touch), `test --compact`.

---

## Verification Phase (run once all 4 phases are merged)

1. `php artisan migrate:fresh --seed` — confirm no migration errors, seeders complete.
2. `php artisan test --compact` — full suite green.
3. `vendor/bin/pint --test` — no style violations.
4. `vendor/bin/phpstan analyse` — no new level-7 errors.
5. Manually browse (or drive via Boost's browser tools):
    - `/animals` — search/filter/paginate
    - `/animals/create` → `/animals/{id}` — verify redirect
    - edit an animal, log a weight
    - open both modals, create a health record + a breeding record (with offspring recording)
    - delete an animal, confirm the modal-based confirm flow
    - `/upcoming` — confirm both lists populate and links work
6. Register a brand-new user through the normal registration flow and confirm: non-null `farm_id`, they own exactly one `Farm`, and they can immediately create an `Animal` with no manual intervention (verifies pitfall #3 fixed).
7. Soft-delete an animal that has a due-soon health record and confirm `/upcoming` still loads without error and simply omits that reminder (verifies pitfall #1 fixed).
