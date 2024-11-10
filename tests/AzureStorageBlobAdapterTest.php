<?php

namespace AzureOss\LaravelAzureStorageBlob\Tests;

use AzureOss\LaravelAzureStorageBlob\AzureStorageBlobAdapter;
use AzureOss\LaravelAzureStorageBlob\AzureStorageBlobServiceProvider;
use AzureOss\Storage\Blob\BlobServiceClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AzureStorageBlobAdapterTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [AzureStorageBlobServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('filesystems.disks.azure', [
            'driver' => 'azure-storage-blob',
            'connection_string' => env('AZURE_STORAGE_CONNECTION_STRING'),
            'container' => env('AZURE_STORAGE_CONTAINER'),
        ]);
    }

    #[Test]
    public function it_resolves_from_manager(): void
    {
        self::assertInstanceOf(AzureStorageBlobAdapter::class, Storage::disk('azure'));
    }

    #[Test]
    public function driver_works(): void
    {
        BlobServiceClient::fromConnectionString(env('AZURE_STORAGE_CONNECTION_STRING'))
            ->getContainerClient(env('AZURE_STORAGE_CONTAINER'))
            ->createIfNotExists();

        $driver = Storage::disk('azure');

        // cleanup from previous test runs
        $driver->deleteDirectory('');

        self::assertFalse($driver->exists('file.text'));

        $driver->put('file.txt', 'content');

        self::assertTrue($driver->exists('file.txt'));

        self::assertEquals(
            'content',
            $driver->get('file.txt')
        );
        self::assertEquals(
            'content',
            Http::get($driver->temporaryUrl('file.txt', now()->addMinute()))->body()
        );
        self::assertEquals(
            'content',
            Http::get($driver->url('file.txt'))->body()
        );

        $driver->copy('file.txt', 'file2.txt');

        self::assertTrue($driver->exists('file2.txt'));

        $driver->move('file2.txt', 'file3.txt');

        self::assertFalse($driver->exists('file2.txt'));
        self::assertTrue($driver->exists('file3.txt'));

        self::assertCount(2, $driver->allFiles());

        $driver->deleteDirectory('');

        self::assertCount(0, $driver->allFiles());
    }
}
