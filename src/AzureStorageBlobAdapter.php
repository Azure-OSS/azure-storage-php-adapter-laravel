<?php

namespace AzureOss\LaravelAzureStorageBlob;

use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use AzureOss\Storage\Blob\BlobServiceClient;
use Illuminate\Filesystem\FilesystemAdapter;
use AzureOss\Storage\Blob\BlobContainerClient;
use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;

/**
 * @internal
 *
 * @property AzureBlobStorageAdapter $adapter
 */
final class AzureStorageBlobAdapter extends FilesystemAdapter
{
    private BlobContainerClient $containerClient;

    /**
     * @param  array{connection_string: string, container: string, prefix?: string, root?: string}  $config
     */
    public function __construct(array $config)
    {
        $serviceClient = BlobServiceClient::fromConnectionString($config['connection_string']);
        $this->containerClient = $serviceClient->getContainerClient($config['container']);
        $adapter = new AzureBlobStorageAdapter($this->containerClient, $config['prefix'] ?? $config['root'] ?? '');

        parent::__construct(
            new Filesystem($adapter, $config),
            $adapter,
            $config
        );
    }

    public function url($path)
    {
        return $this->adapter->publicUrl($path, new Config);
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

    /**
     * @param string $path The path of the blob.
     * @param array<string> $tags The tags to set.
     * @return void
     */
    public function setTags(string $path, array $tags)
    {
        $this->containerClient->getBlobClient($path)->setTags($tags);
    }

    /**
     * @param string $path The path of the blob.
     * @return array<string> The tags for the blob.
     */
    public function getTags(string $path): array
    {
        return $this->containerClient->getBlobClient($path)->getTags();
    }
}
