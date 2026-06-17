<?php

namespace App\Modules\Cms\Support;

use App\Modules\Cms\Enums\CmsBlockType;
use InvalidArgumentException;

final readonly class CmsContent
{
    public const SCHEMA_VERSION = 1;

    /**
     * @param  array<string, mixed>  $content
     * @return array{schema_version: int, blocks: list<array<string, mixed>>}
     */
    public function normalize(array $content): array
    {
        if (! array_key_exists('blocks', $content)) {
            $content = [
                'schema_version' => self::SCHEMA_VERSION,
                'blocks' => $this->legacyBodyToBlocks($content),
            ];
        }

        $schemaVersion = (int) ($content['schema_version'] ?? self::SCHEMA_VERSION);

        if ($schemaVersion !== self::SCHEMA_VERSION) {
            throw new InvalidArgumentException('Unsupported CMS content schema version.');
        }

        $blocks = $content['blocks'];

        if (! is_array($blocks)) {
            throw new InvalidArgumentException('CMS content blocks must be an array.');
        }

        return [
            'schema_version' => self::SCHEMA_VERSION,
            'blocks' => array_values(array_map(
                fn (mixed $block): array => $this->normalizeBlock($block),
                $blocks,
            )),
        ];
    }

    /**
     * @param  array<string, mixed>  $content
     * @return list<array<string, mixed>>
     */
    private function legacyBodyToBlocks(array $content): array
    {
        $body = trim((string) ($content['body'] ?? ''));

        if ($body === '') {
            return [];
        }

        return [
            [
                'type' => CmsBlockType::Text->value,
                'data' => [
                    'body' => $body,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeBlock(mixed $block): array
    {
        if (! is_array($block)) {
            throw new InvalidArgumentException('CMS content block must be an array.');
        }

        $type = CmsBlockType::tryFrom((string) ($block['type'] ?? ''));

        if (! $type instanceof CmsBlockType) {
            throw new InvalidArgumentException('Unsupported CMS content block type.');
        }

        $data = $block['data'] ?? [];

        if (! is_array($data)) {
            throw new InvalidArgumentException('CMS content block data must be an array.');
        }

        return [
            'id' => $this->normalizeId($block['id'] ?? null),
            'type' => $type->value,
            'data' => $this->normalizeBlockData($type, $data),
        ];
    }

    private function normalizeId(mixed $id): string
    {
        $normalized = trim((string) $id);

        if ($normalized === '') {
            return 'block_'.bin2hex(random_bytes(8));
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeBlockData(CmsBlockType $type, array $data): array
    {
        return match ($type) {
            CmsBlockType::Heading => [
                'text' => $this->requiredString($data, 'text'),
                'level' => $this->headingLevel($data['level'] ?? 2),
            ],
            CmsBlockType::Hero => [
                'title' => $this->requiredString($data, 'title'),
                'subtitle' => $this->nullableString($data['subtitle'] ?? null),
                'image_url' => $this->nullableString($data['image_url'] ?? null),
            ],
            CmsBlockType::Image => [
                'url' => $this->requiredString($data, 'url'),
                'alt' => $this->nullableString($data['alt'] ?? null),
                'caption' => $this->nullableString($data['caption'] ?? null),
            ],
            CmsBlockType::Text => [
                'body' => $this->requiredString($data, 'body'),
            ],
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredString(array $data, string $key): string
    {
        $value = trim((string) ($data[$key] ?? ''));

        if ($value === '') {
            throw new InvalidArgumentException("CMS block field [{$key}] is required.");
        }

        return $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private function headingLevel(mixed $value): int
    {
        $level = (int) $value;

        if ($level < 1 || $level > 6) {
            throw new InvalidArgumentException('CMS heading level must be between 1 and 6.');
        }

        return $level;
    }
}
