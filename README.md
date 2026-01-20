 <h1 align="center">Maestro</h1>
<p align="center"><b>Laravel Starter Kit Orchestrator</b></p>

## Starter Kits

We have two stacks of starter kits: **Inertia** and **Livewire**

For these stacks, we have ten different variations of starter kits:

### Livewire Starter Kits

1. **Blank:** a minimal starter kit with no authentication scaffolding.
2. **Fortify:** starter kit using *Laravel Fortify* for authentication.
3. **Fortify (Multi-file Components):** the same as above, but having the view separated from the component code.
4. **WorkOS:** starter kit using **[WorkOS](https://workos.com)** for authentication.

### Inertia Starter Kits

1. **Blank React:** a minimal *React* starter kit with no authentication scaffolding.
2. **Fortify React:** *React* starter kit using *Laravel Fortify* for authentication.
3. **WorkOS React:** *React* starter kit using **[WorkOS](https://workos.com)** for authentication.
4. **Blank Vue:** a minimal *Vue* starter kit with no authentication scaffolding.
5. **Fortify Vue:** *Vue* starter kit using *Laravel Fortify* for authentication.
6. **WorkOS Vue:** *Vue* starter kit using **[WorkOS](https://workos.com)** for authentication.

### Starter Kit Hierarchy

The Starter Kits have a hierarchy for the files:

```bash
# INERTIA STARTER KITS
Blank -> Base -> UI Layer (React/Vue) -> Auth Layer (Fortify/WorkOS)

# LIVEWIRE STARTER KIT
Blank -> Base -> Auth Layer (Fortify/WorkOS) -> Components (Fortify only variant)
```

Where `Blank` has the shared files across all variants, and so on.

When applying a change inside the `build` folder, **Maestro** is smart enough to know where that change should be
replicated to, always preferring to apply the change to the customization, so if a file exists both in the
`Blank` and the `Auth Layer`, the change is replicated to the `Auth Layer` by default.

## Orchestrator

The `orchestrator` is a simple Laravel application that's responsible for building and running the
starter kits, so you can make changes to any of the Starter Kits.

It streamlines the changes by replicating the changes made in the `build` folder - the Starter Kit you're running - to the
`kits` folder where the files for the different Starter Kits live.

## Contributing

To contribute changes to any of the starter kits, you need to build and run the one you want to update.

### Building a starter kit

Use this command to build:

```bash
php artisan build
```

This will prompt you to build the starter kit you want. In alternative you can use the `--kit`
parameter and the `--workos`, `--components` or `--blank` flags to build directly:

```bash
php artisan build --kit=vue # Builds the Vue (Fortify) starter kit
php artisan build --kit=react --workos # Builds the React (WorkOS) starter kit
php artisan build --kit=livewire --blank # Builds Blank Livewire starter kit
```

When building a **WorkOS** variant for a starter kit, you can add your **WorkOS** client ID and the
API key in the `orchestrator/.env` file, with this, when running `composer kit:run`, it will copy these
values to the built starter kit.

### Running the built starter kit

You can use this command to run the built starter kit at `http://localhost:8000` with hot-reloading enabled:

```bash
composer kit:run
```

This will start both the development server and a file watcher that automatically copies changes from the `build`
folder to the correct `kits` ones.

### Submitting changes

After making the changes and testing that they are working, **Maestro** already copied the changes to the `kits` folder,
so you just need to commit and create a PR with these changes, after your PR is merged, **Maestro** will create PRs for
the needed starter kits based in the changes that were applied.
