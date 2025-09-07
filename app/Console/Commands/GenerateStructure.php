<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:structure {--depth=5 : The maximum depth of the directory structure}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a text file with the project file structure (excluding vendor, cache, and unnecessary files), with a maximum depth, and folders ending with "/".';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rootPath = base_path();
        $maxDepth = $this->option('depth');
        $structure = $this->generateStructure($rootPath, '', 0, $maxDepth);

        File::put(storage_path('app/project_structure.txt'), $structure);

        $this->info('Project structure generated and saved to storage/app/project_structure.txt');

        return Command::SUCCESS;
    }

    /**
     * Recursively generate the file structure, skipping specified directories and files, with a maximum depth, and folders ending with "/".
     *
     * @param string $path
     * @param string $prefix
     * @param int $currentDepth
     * @param int $maxDepth
     * @return string
     */
    protected function generateStructure(string $path, string $prefix = '', int $currentDepth = 0, int $maxDepth = 5): string
    {
        if ($currentDepth > $maxDepth) {
            return ''; // Stop recursion if maximum depth is reached
        }

        $files = File::files($path);
        $directories = File::directories($path);
        $output = '';
        $all = array_merge($directories, $files);

        $count = count($all);
        $i = 0;

        foreach ($all as $item) {
            $i++;
            $isLast = ($i === $count);
            $itemName = basename($item);

            // Skip specified directories and files
            if ($itemName === 'vendor' || $itemName === 'node_modules' || $itemName === 'bootstrap/cache' || $itemName === '.git' || str_ends_with($itemName, '.lock') || str_ends_with($itemName, '.json') || str_ends_with($itemName, '.log')) {
                continue;
            }

            // Add "/" to folder names
            if (is_dir($item)) {
                $itemName .= '/';
            }

            $output .= $prefix . ($isLast ? '└── ' : '├── ') . $itemName . PHP_EOL;

            if (is_dir($item)) {
                $newPrefix = $prefix . ($isLast ? '    ' : '│   ');
                $output .= $this->generateStructure($item, $newPrefix, $currentDepth + 1, $maxDepth);
            }
        }

        return $output;
    }
}