<?php

namespace AzureOss\LaravelAzureStorageBlob;

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
            return new AzureStorageBlobAdapter($config);
        });
    }
}
