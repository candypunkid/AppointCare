<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModule extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Scaffold a new AppointCare module';

    protected array $folders = [
        'Controllers',
        'Services',
        'Repositories/Contracts',
        'Models',
        'Requests',
        'Resources',
        'Events',
        'Listeners',
        'Jobs',
        'Policies',
        'Exceptions',
        'Providers',
    ];

    public function handle(Filesystem $files): int
    {
        $name = $this->argument('name');
        $base = base_path("Modules/{$name}");

        foreach ($this->folders as $folder) {
            $path = "{$base}/{$folder}";
            if (! $files->isDirectory($path)) {
                $files->makeDirectory($path, 0755, true, true);
            }
            $files->put("{$path}/.gitkeep", "");
        }

        // Create the Service Provider
        $this->createServiceProvider($name, $base, $files);

        // Create the routes file
        $files->put("{$base}/routes.php", "<?php\n\n// {$name} module routes\n");

        $this->info("Module [{$name}] scaffolded at app/Modules/{$name}");

        return 0;
    }

    protected function createServiceProvider(string $name, string $base, Filesystem $files): void
    {
        $stub = <<<PHP
<?php

namespace App\Modules\\{$name}\Providers;

use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repository interfaces to implementations here
    }

    public function boot(): void
    {
        if (file_exists(__DIR__ . '/../routes.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        }
    }
}
PHP;

        // Ensure provider path matches existing module layout (app/Providers)
        $files->put("{$base}/app/Providers/{$name}ServiceProvider.php", $stub);
    }
}
