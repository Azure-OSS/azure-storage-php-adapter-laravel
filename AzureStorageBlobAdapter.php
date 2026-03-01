<?php

declare(strict_types=1);

namespace AzureOss\Storage\BlobLaravel;

use AzureOss\Storage\Blob\BlobServiceClient;
use AzureOss\Storage\BlobFlysystem\AzureBlobStorageAdapter;
use AzureOss\Storage\Common\Auth\ClientSecretCredential;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use Psr\Http\Message\UriInterface;

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
     * @param  array{connection_string?: string, endpoint?: string, account_name?: string, endpoint_suffix?: string, tenant_id?: string, client_id?: string, client_secret?: string, container: string, prefix?: string, root?: string}  $config
     */
    public function __construct(array $config)
    {
        $serviceClient = self::createBlobServiceClient($config);
        $containerClient = $serviceClient->getContainerClient($config['container']);
        $this->canProvideTemporaryUrls = $containerClient->canGenerateSasUri();
        $adapter = new AzureBlobStorageAdapter($containerClient, $config['prefix'] ?? $config['root'] ?? '');

        parent::__construct(
            new Filesystem($adapter, $config),
            $adapter,
            $config,
        );
    }

    /**
     * @param  array{connection_string?: string, endpoint?: string, account_name?: string, endpoint_suffix?: string, tenant_id?: string, client_id?: string, client_secret?: string, container: string}  $config
     */
    private static function createBlobServiceClient(array $config): BlobServiceClient
    {
        $connectionString = $config['connection_string'] ?? null;
        if (is_string($connectionString) && $connectionString !== '') {
            return BlobServiceClient::fromConnectionString($connectionString);
        }

        $tenantId = $config['tenant_id'] ?? null;
        $clientId = $config['client_id'] ?? null;
        $clientSecret = $config['client_secret'] ?? null;

        if (! is_string($tenantId) || ! is_string($clientId) || ! is_string($clientSecret)) {
            throw new \InvalidArgumentException('Token-based credentials require [tenant_id], [client_id], and [client_secret].');
        }

        $uri = self::buildBlobEndpointUri($config);
        $credential = new ClientSecretCredential($tenantId, $clientId, $clientSecret);

        return new BlobServiceClient($uri, $credential);
    }

    /**
     * @param  array{endpoint?: string, account_name?: string, endpoint_suffix?: string}  $config
     */
    private static function buildBlobEndpointUri(array $config): UriInterface
    {
        $endpoint = $config['endpoint'] ?? null;
        if (is_string($endpoint) && $endpoint !== '') {
            return new Uri(rtrim($endpoint, '/').'/');
        }

        $accountName = $config['account_name'] ?? null;
        if (! is_string($accountName) || $accountName === '') {
            throw new \InvalidArgumentException('Either [endpoint] or [account_name] must be provided for token-based credentials.');
        }

        $endpointSuffix = $config['endpoint_suffix'] ?? 'core.windows.net';
        $endpoint = sprintf('https://%s.blob.%s', $accountName, $endpointSuffix);

        return new Uri($endpoint.'/');
    }

    public function url($path)
    {
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
