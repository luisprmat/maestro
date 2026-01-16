<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

class BuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build {--kit= : The starter kit to build (Livewire, React, or Vue)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds one of the Starter Kits';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $availableKits = config('maestro.starter_kits');
        $kit = $this->option('kit');

        if ($kit) {
            $kit = $this->validateKit($kit, $availableKits);

            if (! $kit) {
                return self::FAILURE;
            }
        } else {
            $kit = select(
                label: 'Which starter kit would you like to build?',
                options: $availableKits,
            );
        }

        info("Building {$kit} starter kit...");

        if ($kit === 'Livewire') {
            return $this->buildLivewireKit();
        }

        return $this->buildInertiaKit($kit);
    }

    /**
     * Validate the kit option against available kits.
     */
    protected function validateKit(string $kit, array $availableKits): ?string
    {
        foreach ($availableKits as $availableKit) {
            if (strtolower($availableKit) === strtolower($kit)) {
                return $availableKit;
            }
        }

        error("Invalid kit '{$kit}'. Available kits are: ".implode(', ', $availableKits));

        return null;
    }

    /**
     * Build the Livewire starter kit.
     */
    protected function buildLivewireKit(): int
    {
        info('Livewire kit build is not implemented yet.');

        return self::SUCCESS;
    }

    /**
     * Build an Inertia starter kit (React or Vue).
     */
    protected function buildInertiaKit(string $kit): int
    {
        $buildPath = base_path('build');
        $basePath = base_path('kits/Inertia/Base');
        $kitPath = base_path("kits/Inertia/{$kit}");

        if (File::exists($buildPath)) {
            File::deleteDirectory($buildPath);
        }
        File::makeDirectory($buildPath, 0755, true);

        info('Copying Base kit files...');
        File::copyDirectory($basePath, $buildPath);

        info("Copying {$kit} kit files...");
        File::copyDirectory($kitPath, $buildPath);

        info('Replacing component placeholders...');
        $this->replacePlaceholders($buildPath, strtolower($kit));

        info('Replacing variant placeholders...');
        $this->replaceVariantPlaceholder($buildPath, strtolower($kit));

        info("{$kit} starter kit built successfully in the 'build' folder.");

        return self::SUCCESS;
    }

    /**
     * Replace all placeholders in the build folder.
     */
    protected function replacePlaceholders(string $buildPath, string $kit): void
    {
        $uiComponents = config('maestro.ui_components');

        $searchPaths = [
            $buildPath.'/app/Http/Controllers',
            $buildPath.'/app/Providers',
            $buildPath.'/routes',
            $buildPath.'/tests',
        ];

        foreach ($searchPaths as $searchPath) {
            if (! File::exists($searchPath)) {
                continue;
            }

            $files = File::allFiles($searchPath);

            foreach ($files as $file) {
                $content = $file->getContents();
                $modified = false;

                foreach ($uiComponents as $key => $values) {
                    if (! isset($values[$kit])) {
                        continue;
                    }

                    $placeholder = "{{{$key}}}";
                    $replacement = $values[$kit];

                    if (str_contains($content, $placeholder)) {
                        $content = str_replace($placeholder, $replacement, $content);
                        $modified = true;
                    }
                }

                if ($modified) {
                    File::put($file->getPathname(), $content);
                }
            }
        }
    }

    /**
     * Replace the {{variant}} placeholder with the kit name.
     */
    protected function replaceVariantPlaceholder(string $buildPath, string $kit): void
    {
        $composerPath = $buildPath.'/composer.json';

        if (! File::exists($composerPath)) {
            return;
        }

        $content = File::get($composerPath);

        if (str_contains($content, '{{variant}}')) {
            $content = str_replace('{{variant}}', $kit, $content);
            File::put($composerPath, $content);
        }
    }
}
