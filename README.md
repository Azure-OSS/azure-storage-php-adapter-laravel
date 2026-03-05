# Azure Storage Blob filesystem driver for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/azure-oss/storage-blob-laravel.svg)](https://packagist.org/packages/azure-oss/storage-blob-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dm/azure-oss/storage-blob-laravel)](https://packagist.org/packages/azure-oss/storage-blob-laravel)

> [!IMPORTANT]
> **Issues must be reported in the [monorepo issue tracker](https://github.com/Azure-OSS/azure-storage-monorepo/issues).** Please do not create issues in individual package repositories.

## Minimum Requirements

* PHP 8.1 or above

## Install

Install the package using composer:
```shell
composer require azure-oss/storage-blob-laravel
```


Then add this to the disks section of config/filesystems.php.

**Using a storage account connection string (Shared Key):**
```php
'azure' => [
    'driver' => 'azure-storage-blob',
    'connection_string' => env('AZURE_STORAGE_CONNECTION_STRING'),
    'container' => env('AZURE_STORAGE_CONTAINER'),
],
```

**Using Microsoft Entra ID (formerly Azure Active Directory) credentials:**

Token-based authentication uses a Microsoft Entra ID (Azure AD) application (service principal) with a client secret. This avoids storing account keys and enables modern authentication scenarios such as workload identity and managed identity.

```php
'azure' => [
    'driver' => 'azure-storage-blob',
    'endpoint' => env('AZURE_STORAGE_ENDPOINT'), // e.g. https://mystorageaccount.blob.core.windows.net
    // Or use account_name (endpoint will be built as https://{account_name}.blob.core.windows.net):
    // 'account_name' => env('AZURE_STORAGE_ACCOUNT_NAME'),
    // 'endpoint_suffix' => env('AZURE_STORAGE_ENDPOINT_SUFFIX', 'core.windows.net'), // for Azure China, etc.
    'tenant_id' => env('AZURE_STORAGE_TENANT_ID'),
    'client_id' => env('AZURE_STORAGE_CLIENT_ID'),
    'client_secret' => env('AZURE_STORAGE_CLIENT_SECRET'),
    'container' => env('AZURE_STORAGE_CONTAINER'),
],
```

> **Note:** When using Microsoft Entra ID credentials, this driver cannot generate shared access signatures (SAS). The `providesTemporaryUrls()` method will return `false`.

## Public Containers

If your container is configured for public access, you can enable direct public URLs:

```php
'azure' => [
    'driver' => 'azure-storage-blob',
    // credentials...
    'container' => env('AZURE_STORAGE_CONTAINER'),
    'is_public_container' => true,
],
```

When `is_public_container` is enabled, `Storage::disk('azure')->url($path)` returns the direct blob URL (no SAS).

## Usage

Usage follows Laravel's filesystem conventions. For uploading, retrieving, and managing files, refer to the official Laravel documentation: 📖 [Laravel Filesystem Documentation](https://laravel.com/docs/12.x/filesystem)

## Upload Options (HTTP Headers)

You can set blob HTTP headers (including `Cache-Control`) by passing Flysystem options as the 3rd argument to `put()`:

```php
use Illuminate\Support\Facades\Storage;

Storage::disk('azure')->put('assets/app.css', $css, [
    'httpHeaders' => [
        'cacheControl' => 'public, max-age=31536000',
        'contentType' => 'text/css',
    ],
]);
```

Supported `httpHeaders` keys:

* `cacheControl`
* `contentDisposition`
* `contentEncoding`
* `contentHash`
* `contentLanguage`
* `contentType`

## Support

Do you need help, do you want to talk to us, or is there anything else?

Join us at:

* [Github Discussions](https://github.com/Azure-OSS/azure-storage-monorepo/discussions)
* [Slack](https://join.slack.com/t/azure-oss/shared_invite/zt-2lw5knpon-mqPM_LIuRZUoH02AY8uiYw)

## License

Azure-Storage-PHP-Adapter-Flysystem is released under the MIT License. See [LICENSE](./LICENSE) for details.

## PHP Version Support Policy

The maintainers of this package add support for a PHP version following its initial release and drop support for a PHP version once it has reached its end of security support.
