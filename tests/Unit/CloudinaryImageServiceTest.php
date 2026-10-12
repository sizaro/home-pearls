<?php

namespace Tests\Unit;

use App\Services\CloudinaryImageService;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CloudinaryImageServiceTest extends TestCase
{
    public function test_it_uploads_an_image_to_the_configured_collection(): void
    {
        config()->set('services.cloudinary', [
            'cloud_name' => 'test-cloud',
            'api_key' => 'test-key',
            'api_secret' => 'test-secret',
            'folder' => 'home-pearls/testing',
        ]);

        $uploadApi = Mockery::mock();
        $uploadApi->shouldReceive('upload')
            ->once()
            ->withArgs(fn ($path, $options) => is_string($path) && $options['folder'] === 'home-pearls/testing/products')
            ->andReturn([
                'secure_url' => 'https://res.cloudinary.com/test-cloud/image/upload/home-pearls/testing/products/chair.jpg',
                'public_id' => 'home-pearls/testing/products/chair',
            ]);

        $client = Mockery::mock(Cloudinary::class);

        $image = UploadedFile::fake()->create('chair.jpg', 100, 'image/jpeg');
        $uploaded = (new CloudinaryImageService($client, $uploadApi))->upload($image, 'products');

        $this->assertSame('home-pearls/testing/products/chair', $uploaded['public_id']);
        $this->assertStringStartsWith('https://', $uploaded['url']);
    }

    public function test_it_refuses_to_delete_an_asset_outside_the_configured_folder(): void
    {
        config()->set('services.cloudinary', [
            'cloud_name' => 'test-cloud',
            'api_key' => 'test-key',
            'api_secret' => 'test-secret',
            'folder' => 'home-pearls/testing',
        ]);

        $client = Mockery::mock(Cloudinary::class);
        $uploadApi = Mockery::mock();

        $this->expectException(InvalidArgumentException::class);
        (new CloudinaryImageService($client, $uploadApi))->delete('home-pearls/trial/products/chair', 'products');
    }
}
