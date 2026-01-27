<?php

declare(strict_types=1);

namespace AzureOss\Storage\BlobLaravel;

use AzureOss\Storage\Blob\BlobServiceClient;
use AzureOss\Storage\BlobFlysystem\AzureBlobStorageAdapter;
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
     * Whether the configuration of this adapter allows temporary URLs.
     */
    public bool $canProvideTemporaryUrls;

    /**
     * @param  array{connection_string: string, container: string, prefix?: string, root?: string}  $config
     */
    public function __construct(array $config)
    {
        $serviceClient = BlobServiceClient::fromConnectionString($config['connection_string']);
        $containerClient = $serviceClient->getContainerClient($config['container']);
        $this->canProvideTemporaryUrls = $containerClient->canGenerateSasUri();
        $adapter = new AzureBlobStorageAdapter($containerClient, $config['prefix'] ?? $config['root'] ?? '');

        parent::__construct(
            new Filesystem($adapter, $config),
            $adapter,
            $config,
        );
    }

    public function url($path)
    {
        // Handle container URL request (passes '/' or '')
        if ($path === '/' || $path === '') {
            return $this->baseUrl ?? '';
        }

        // Normalize path - remove leading slashes that Azure doesn't support
        $path = ltrim($path, '/');
        
        return $this->adapter->publicUrl($path, new Config);
    }

    /**
     * Determine if temporary URLs can be generated.
     *
     * @return bool
     */
    public function providesTemporaryUrls()
    {
        return $this->canProvideTemporaryUrls;
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @return string
     */
    /** @phpstan-ignore-next-line */
    public function temporaryUrl($path, $expiration, array $options = [])
    {
        return $this->adapter->temporaryUrl(
            $path,
            $expiration,
            new Config(array_merge(['permissions' => 'r'], $options)),
        );
    }

    /**
     * Get a temporary upload URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @return array{url: string, headers: array<string, string>}
     */
    /** @phpstan-ignore-next-line */
    public function temporaryUploadUrl($path, $expiration, array $options = [])
    {
        $url = $this->adapter->temporaryUrl(
            $path,
            $expiration,
            new Config(array_merge(['permissions' => 'cw'], $options)),
        );
        $contentType = isset($options['content-type']) && is_string($options['content-type'])
            ? $options['content-type']
            : 'application/octet-stream';

        return [
            'url' => $url,
            'headers' => [
                'x-ms-blob-type' => 'BlockBlob',
                'Content-Type' => $contentType,
            ],
        ];
    }
}
