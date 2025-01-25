# Azure Storage Blob filesystem driver for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/azure-oss/storage-blob-laravel.svg)](https://packagist.org/packages/azure-oss/storage-blob-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dm/azure-oss/storage-blob-laravel)](https://packagist.org/packages/azure-oss/storage-blob-laravel)

## Minimum Requirements

* PHP 8.1 or above

## Install

Install the package using composer:
```shell
composer require azure-oss/storage-blob-laravel
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

### Example: Upload a File

Example of how to upload a file to Azure Blob Storage:

```php
use Illuminate\Support\Facades\Storage;

$file = $request->file('file'); // Assuming a file upload from a request

// Generate a unique file name with extension
$fileName = uniqid() . '.' . $file->getClientOriginalExtension();

// Upload the file to Azure Blob Storage
$path = Storage::disk('azure')->putFileAs('', $file, $fileName);

return response()->json([
    'message' => 'File uploaded successfully' 
]);
```

### Example: Get File URL

#### Permanent URL

To get a public URL (if the blob container is set to allow public access):

```php
use Illuminate\Support\Facades\Storage;

$filePath = 'example-file.txt'; // Relative path of the file in the container

$url = Storage::disk('azure')->url($filePath);

return response()->json([
    'file_url' => $url, // Permanent public URL
]);
```

#### Temporary URL

To generate a temporary URL (with an expiration time for secure access):

```php
use Illuminate\Support\Facades\Storage;

$filePath = 'example-file.txt'; // Relative path of the file in the container

$temporaryUrl = Storage::disk('azure')->temporaryUrl(
    $filePath,
    now()->addMinutes(30) // Set the expiration time, e.g., 30 minutes
);

return response()->json([
    'temporary_url' => $temporaryUrl, // Temporary access URL
]);
```

## Support

Do you need help, do you want to talk to us, or is there anything else?

Join us at:

* [Github Discussions](https://github.com/Azure-OSS/azure-storage-php/discussions)
* [Slack](https://join.slack.com/t/azure-oss/shared_invite/zt-2lw5knpon-mqPM_LIuRZUoH02AY8uiYw)

## License

Azure-Storage-PHP-Adapter-Flysystem is released under the MIT License. See [LICENSE](./LICENSE) for details.

## PHP Version Support Policy

The maintainers of this package add support for a PHP version following its initial release and drop support for a PHP version once it has reached its end of security support.
