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
