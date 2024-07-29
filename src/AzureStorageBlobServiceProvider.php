<?php

namespace AzureOss\LaravelAzureStorageBlob;

use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;
use AzureOss\Storage\Blob\BlobServiceClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

/**
 * @internal
 */
final class AzureStorageBlobServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('azure-storage-blob', function (Application $app, array $config): FilesystemAdapter {
            $serviceClient = BlobServiceClient::fromConnectionString($config['connection_string']);
            $containerClient = $serviceClient->getContainerClient($config['container']);
            $adapter = new AzureBlobStorageAdapter($containerClient, $config['prefix'] ?? null);

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
