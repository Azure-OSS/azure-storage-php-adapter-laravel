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
            /** @var array<string, mixed> $config */
            self::assertStringConfig($config, 'container', required: true);
            self::assertStringConfig($config, 'prefix');
            self::assertStringConfig($config, 'root');
            self::assertStringConfig($config, 'tenant_id');
            self::assertStringConfig($config, 'client_id');
            self::assertStringConfig($config, 'client_secret');
            self::assertStringConfig($config, 'endpoint');
            self::assertStringConfig($config, 'endpoint_suffix');
            self::assertStringConfig($config, 'account_name');
            self::assertStringConfig($config, 'connection_string');

            $hasConnectionString = isset($config['connection_string']);
            $hasEndpoint = isset($config['endpoint']);
            $hasAccountName = isset($config['account_name']);
            $hasTokenCredentials = isset($config['tenant_id'], $config['client_id'], $config['client_secret'])
                && ($hasEndpoint || $hasAccountName);

            if (! $hasConnectionString && ! $hasTokenCredentials) {
                throw new \InvalidArgumentException(
                    'Either [connection_string] or [endpoint, tenant_id, client_id, client_secret] must be provided in the disk configuration.'
                );
            }

            if ($hasConnectionString && $hasTokenCredentials) {
                throw new \InvalidArgumentException(
                    'Cannot use both [connection_string] and token-based credentials in the disk configuration.'
                );
            }

            /** @phpstan-ignore-next-line */
            return new AzureStorageBlobAdapter($config);
        });
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private static function assertStringConfig(array $config, string $key, bool $required = false): void
    {
        if (! array_key_exists($key, $config) || $config[$key] === null) {
            if ($required) {
                throw new \InvalidArgumentException("The [{$key}] must be a string in the disk configuration.");
            }

            return;
        }

        if (! is_string($config[$key])) {
            throw new \InvalidArgumentException("The [{$key}] must be a string in the disk configuration.");
        }
    }
}
