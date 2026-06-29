# Azure Storage Blob filesystem driver for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/azure-oss/storage-blob-laravel.svg)](https://packagist.org/packages/azure-oss/storage-blob-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dt/azure-oss/storage-blob-laravel)](https://packagist.org/packages/azure-oss/storage-blob-laravel)

A Laravel filesystem driver for Azure Blob Storage built on top of the Flysystem adapter.

## Install

```shell
composer require azure-oss/storage-blob-laravel
```

## Configuration

```php
# config/filesystems.php

'azure' => [
    'driver' => 'azure-storage-blob',
    'connection_string' => env('AZURE_STORAGE_CONNECTION_STRING'),
    'container' => env('AZURE_STORAGE_CONTAINER'),
],
```

Besides shared key via connection string, this driver supports additional authentication methods such as Entra ID / token-based credentials, managed identity, workload identity, and shared key via account key. See the [installation docs](https://azure-oss.github.io/category/storage-blob-laravel/installation) for configuration examples.

## Documentation

You can read the documentation [here](https://azure-oss.github.io/category/storage-blob-laravel).

## Related packages

- **[azure-oss/storage](https://packagist.org/packages/azure-oss/storage)** — Meta package for the Storage SDKs
- **[azure-oss/storage-common](https://packagist.org/packages/azure-oss/storage-common)** — Shared authentication, HTTP, and SAS primitives
- **[azure-oss/storage-blob-flysystem](https://packagist.org/packages/azure-oss/storage-blob-flysystem)** — Flysystem adapter
- **[azure-oss/storage-blob-flysystem-bundle](https://packagist.org/packages/azure-oss/storage-blob-flysystem-bundle)** — Symfony Flysystem bundle
- **[azure-oss/storage-blob](https://packagist.org/packages/azure-oss/storage-blob)** — Blob Storage SDK
- **[azure-oss/storage-queue](https://packagist.org/packages/azure-oss/storage-queue)** — Queue Storage SDK
- **[azure-oss/storage-queue-laravel](https://packagist.org/packages/azure-oss/storage-queue-laravel)** — Laravel queue connector
- **[azure-oss/storage-file-share](https://packagist.org/packages/azure-oss/storage-file-share)** — File Share SDK
- **[azure-oss/identity](https://packagist.org/packages/azure-oss/identity)** — Microsoft Entra ID token authentication

## Maintenance

This package is part of the community-maintained `azure-oss` Azure SDKs for PHP. It is an independent project and is not affiliated with or endorsed by Microsoft.

## License

This project is released under the MIT License. See [LICENSE](https://github.com/Azure-OSS/azure-php/blob/main/LICENSE) for details.
