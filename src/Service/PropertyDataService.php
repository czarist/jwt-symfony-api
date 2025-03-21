<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class PropertyDataService
{
    private string $dataPath;

    public function __construct(
        private CacheInterface $cache,
        KernelInterface $kernel
    ) {
        $this->dataPath = $kernel->getProjectDir() . '/public/data/';
    }

    public function getMergedProperties(): array
    {
        return $this->cache->get('merged_properties', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $data1 = $this->loadJson('source1.json');
            $data2 = $this->loadJson('source2.json');

            return array_merge(
                $this->normalizeSource1($data1),
                $this->normalizeSource2($data2)
            );
        });
    }

    private function loadJson(string $filename): array
    {
        $file = $this->dataPath . $filename;

        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $json = json_decode($content, true);

        return is_array($json) ? $json : [];
    }

    private function normalizeSource1(array $data): array
    {
        return array_map(fn ($item) => [
            'id' => $item['propertyId'] ?? null,
            'address' => $item['location'] ?? 'N/A',
            'price' => $item['cost'] ?? 0,
            'source' => 'source1'
        ], $data);
    }

    private function normalizeSource2(array $data): array
    {
        return array_map(fn ($item) => [
            'id' => $item['id'] ?? null,
            'address' => $item['address'] ?? 'N/A',
            'price' => $item['price'] ?? 0,
            'source' => 'source2'
        ], $data);
    }
}
