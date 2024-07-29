<?php

namespace AzureOss\LaravelAzureStorageBlob;

use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;
use AzureOss\Storage\Blob\BlobServiceClient;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;

/**
 * @internal
 */
final class AzureStorageBlobAdapter extends FilesystemAdapter
{
    public function __construct(array $config)
    {
        $serviceClient = BlobServiceClient::fromConnectionString($config['connection_string']);
        $containerClient = $serviceClient->getContainerClient($config['container']);
        $adapter = new AzureBlobStorageAdapter($containerClient, $config['prefix'] ?? "");

        parent::__construct(
            new Filesystem($adapter, $config),
            $adapter,
            $config
        );
    }

    public function temporaryUrl($path, $expiration, array $options = [])
    {
        $options["permissions"] = $options["permissions"] ?? "r";

        return $this->adapter->temporaryUrl($path, $expiration, new Config($options));
    }

    public function temporaryUploadUrl($path, $expiration, array $options = [])
    {
        $options["permissions"] = $options["permissions"] ?? "w";

        return $this->adapter->temporaryUrl($path, $expiration, new Config($options));
    }
}