<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use RuntimeException;

class CloudinaryImageService
{
    private object $uploadApi;

    public function __construct(?Cloudinary $client = null, ?object $uploadApi = null)
    {
        $client ??= new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);

        $this->uploadApi = $uploadApi ?? $client->uploadApi();
    }

    /**
     * @return array{url: string, public_id: string}
     */
    public function upload(UploadedFile $file, string $collection): array
    {
        $this->ensureConfigured();

        $upload = $this->uploadApi->upload($file->getRealPath(), [
            'folder' => $this->folder($collection),
            'resource_type' => 'image',
            'use_filename' => true,
            'unique_filename' => true,
            'overwrite' => false,
            'allowed_formats' => ['jpg', 'jpeg', 'png', 'webp'],
        ]);

        $url = (string) ($upload['secure_url'] ?? '');
        $publicId = (string) ($upload['public_id'] ?? '');

        if ($url === '' || $publicId === '') {
            throw new RuntimeException('Cloudinary did not return a usable image reference.');
        }

        return ['url' => $url, 'public_id' => $publicId];
    }

    public function delete(?string $publicId, string $collection): void
    {
        if (! $publicId) {
            return;
        }

        $this->ensureConfigured();

        if (! str_starts_with($publicId, $this->folder($collection).'/')) {
            throw new InvalidArgumentException('Refusing to delete an image outside this application collection.');
        }

        $result = $this->uploadApi->destroy($publicId, [
            'resource_type' => 'image',
            'invalidate' => true,
        ]);

        if (! in_array($result['result'] ?? null, ['ok', 'not found'], true)) {
            throw new RuntimeException('Cloudinary could not delete the image.');
        }
    }

    private function ensureConfigured(): void
    {
        foreach (['cloud_name', 'api_key', 'api_secret'] as $key) {
            if (blank(config("services.cloudinary.{$key}"))) {
                throw new RuntimeException('Cloudinary is not configured.');
            }
        }
    }

    private function folder(string $collection): string
    {
        $base = trim((string) config('services.cloudinary.folder', 'home-pearls/development'), '/');
        $collection = trim($collection, '/');

        if ($base === '' || $collection === '') {
            throw new InvalidArgumentException('A Cloudinary upload folder is required.');
        }

        return "{$base}/{$collection}";
    }
}
