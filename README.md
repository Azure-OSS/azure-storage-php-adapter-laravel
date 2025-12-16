# Azure Storage Blob filesystem driver for Laravel

A fork of [Azure-OSS/azure-storage-php-adapter-laravel](https://github.com/Azure-OSS/azure-storage-php-adapter-laravel) with BIIGLE-specific modifications.

## Minimum Requirements

* PHP 8.1 or above

## Install

Install the package using composer:
```shell
composer require biigle/laravel-azure-storage
```


Then add this to the disks section of config/filesystems.php:
```php
'azure' => [ 
    'driver' => 'azure-storage-blob',
    'connection_string' => env('AZURE_STORAGE_CONNECTION_STRING'),
    'container' => env('AZURE_STORAGE_CONTAINER'),
],
```

## Usage

Usage follows Laravel's filesystem conventions. For uploading, retrieving, and managing files, refer to the official Laravel documentation: 📖 [Laravel Filesystem Documentation](https://laravel.com/docs/12.x/filesystem)

## Support

Do you need help, do you want to talk to us, or is there anything else?

Join us at:

* [Github Discussions](https://github.com/Azure-OSS/azure-storage-php/discussions)
* [Slack](https://join.slack.com/t/azure-oss/shared_invite/zt-2lw5knpon-mqPM_LIuRZUoH02AY8uiYw)

## License

Azure-Storage-PHP-Adapter-Flysystem is released under the MIT License. See [LICENSE](./LICENSE) for details.

## PHP Version Support Policy

The maintainers of this package add support for a PHP version following its initial release and drop support for a PHP version once it has reached its end of security support.
