<?php

declare(strict_types=1);

namespace AzureOss\Storage\BlobLaravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

/**
 * @internal
 */
final class AzureStorageBlobServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('azure-storage-blob', function (Application $app, array $config): FilesystemAdapter {
            if (! isset($config['connection_string']) || ! is_string($config['connection_string'])) {
                throw new \InvalidArgumentException('The [connection_string] must be a string in the disk configuration.');
            }

            if (! isset($config['container']) && ! is_string($config['container'])) {
                throw new \InvalidArgumentException('The [container] must be a string in the disk configuration.');
            }

            if (isset($config['prefix']) && ! is_string($config['prefix'])) {
                throw new \InvalidArgumentException('The [prefix] must be a string in the disk configuration.');
            }

            if (isset($config['root']) && ! is_string($config['root'])) {
                throw new \InvalidArgumentException('The [root] must be a string in the disk configuration.');
            }

            /** @phpstan-ignore-next-line */
            return new AzureStorageBlobAdapter($config);
        });
    }
}
