<?php

namespace Laranova\Services;

use Faker\Factory;
use Faker\Generator;

class ScriptParser
{
    private array $fakers = [];

    private const FAKER_PATTERN = '/\{\{\s*@faker:(\w+)\s*\}\}/';

    private const FAKER_MAP = [
        'name' => 'name',
        'firstName' => 'firstName',
        'lastName' => 'lastName',
        'fullName' => 'name',
        'email' => 'email',
        'safeEmail' => 'safeEmail',
        'phone' => 'phoneNumber',
        'phoneNumber' => 'phoneNumber',
        'address' => 'address',
        'streetAddress' => 'streetAddress',
        'city' => 'city',
        'country' => 'country',
        'postcode' => 'postcode',
        'text' => 'text',
        'sentence' => 'sentence',
        'paragraph' => 'paragraph',
        'number' => 'randomNumber',
        'randomDigit' => 'randomDigit',
        'int' => 'randomNumber',
        'uuid' => 'uuid',
        'url' => 'url',
        'date' => 'date',
        'dateTime' => 'dateTime',
        'company' => 'company',
        'boolean' => 'boolean',
        'word' => 'word',
        'title' => 'title',
    ];

    public function __construct(
        private readonly string $locale = 'en_US',
    ) {}

    public function parse(string $content): string
    {
        if (!str_contains($content, '@faker:')) {
            return $content;
        }

        $faker = $this->faker();

        return preg_replace_callback(
            self::FAKER_PATTERN,
            fn(array $matches): string => $this->resolve($faker, $matches[1]),
            $content,
        );
    }

    public function parseArray(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->parseArray($value);
            } elseif (is_string($value)) {
                $result[$key] = $this->parse($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function faker(?string $locale = null): Generator
    {
        $locale ??= $this->locale;

        if (!isset($this->fakers[$locale])) {
            $this->fakers[$locale] = Factory::create($locale);
        }

        return $this->fakers[$locale];
    }

    private function resolve(Generator $faker, string $type): string
    {
        $method = self::FAKER_MAP[$type] ?? null;

        if ($method === null) {
            return $faker->word();
        }

        if ($method === 'boolean') {
            return $faker->boolean() ? 'true' : 'false';
        }

        if ($method === 'dateTime') {
            return $faker->dateTime()->format('Y-m-d H:i:s');
        }

        return $faker->{$method}();
    }
}
