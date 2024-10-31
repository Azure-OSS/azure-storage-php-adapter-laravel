<?php

namespace AzureOss\LaravelAzureStorageBlob;

use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;
use AzureOss\Storage\Blob\BlobServiceClient;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;

/**
 * @internal
 *
 * @property AzureBlobStorageAdapter $adapter
 */
final class AzureStorageBlobAdapter extends FilesystemAdapter
{
    /**
     * @param  array{connection_string: string, container: string, root?: string}  $config
     */
    public function __construct(array $config)
    {
        $serviceClient = BlobServiceClient::fromConnectionString($config['connection_string']);
        $containerClient = $serviceClient->getContainerClient($config['container']);
        $adapter = new AzureBlobStorageAdapter($containerClient, $config['root'] ?? '');

        parent::__construct(
            new Filesystem($adapter, $config),
            $adapter,
            $config
        );
    }

    /** @phpstan-ignore-next-line  */
    public function temporaryUrl($path, $expiration, array $options = [])
    {
        return $this->adapter->temporaryUrl(
            $path,
            $expiration,
            new Config(['permissions' => 'r'])
        );
    }
}
