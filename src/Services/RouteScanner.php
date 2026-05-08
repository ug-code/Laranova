<?php

namespace Laranova\Services;

use Illuminate\Routing\RouteAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class RouteScanner
{
    private ?array $excludedPrefixes = null;

    private function excludedPrefixes(): array
    {
        if ($this->excludedPrefixes === null) {
            $this->excludedPrefixes = config('laranova.routes.exclude_prefixes', [
                'laranova',
                'request-docs',
                '_debugbar',
                '_ignition',
                'telescope',
                'sanctum',
            ]);
        }

        return $this->excludedPrefixes;
    }

    public function scan(): Collection
    {
        $routes = Route::getRoutes()->getRoutes();
        $result = collect();

        foreach ($routes as $route) {
            if ($this->shouldExclude($route->uri)) {
                continue;
            }

            if (!is_string($route->action['uses'] ?? null)) {
                continue;
            }

            if (RouteAction::containsSerializedClosure($route->action)) {
                continue;
            }

            /** @var array{0: class-string, 1: string} $callback */
            $callback = Str::parseCallback($route->action['uses']);
            [$controllerClass, $controllerMethod] = $callback;

            $methods = array_values(array_filter(
                $route->methods,
                fn(string $m): bool => $m !== 'HEAD',
            ));

            $rules = $this->extractRules($controllerClass, $controllerMethod);
            $pathParams = $this->extractPathParams($route);
            $hasFile = collect($rules)->contains(fn($rule) =>
                is_string($rule) && (str_contains($rule, 'file') || str_contains($rule, 'image'))
            );

            $result->push([
                'methods' => $methods,
                'uri' => $route->uri,
                'name' => $route->getName() ?? '',
                'controller' => (new ReflectionClass($controllerClass))->getShortName(),
                'controllerClass' => $controllerClass,
                'action' => $controllerMethod,
                'middleware' => $route->middleware(),
                'rules' => $rules,
                'pathParams' => $pathParams,
                'has_file' => $hasFile,
            ]);
        }

        return $result->sortBy('uri')->values();
    }

    private function shouldExclude(string $uri): bool
    {
        foreach ($this->excludedPrefixes() as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function extractRules(string $controllerClass, string $method): array
    {
        try {
            $reflection = new ReflectionMethod($controllerClass, $method);
        } catch (Throwable) {
            return [];
        }

        $rules = [];

        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();

            if ($type === null || !method_exists($type, 'getName')) {
                continue;
            }

            $className = $type->getName();

            if (!class_exists($className)) {
                continue;
            }

            try {
                $reflectionClass = new ReflectionClass($className);

                $request = $reflectionClass->isInstantiable()
                    ? $reflectionClass->newInstance()
                    : $reflectionClass->newInstanceWithoutConstructor();

                if (method_exists($request, 'rules')) {
                    $rules = array_merge($rules, $this->flattenRules($request->rules()));
                }
            } catch (Throwable) {
                // Skip if unable to instantiate
            }
        }

        return $rules;
    }

    private function flattenRules(array $rawRules): array
    {
        $flattened = [];

        foreach ($rawRules as $field => $rule) {
            if (is_array($rule)) {
                $flattened[$field] = collect($rule)
                    ->map(fn($r): string => $r instanceof \Illuminate\Contracts\Validation\Rule
                        ? get_class($r)
                        : (is_object($r) ? get_class($r) : (string) $r))
                    ->implode('|');
            } elseif (is_string($rule)) {
                $flattened[$field] = $rule;
            } elseif ($rule instanceof \Illuminate\Contracts\Validation\Rule) {
                $flattened[$field] = get_class($rule);
            } else {
                $flattened[$field] = (string) $rule;
            }
        }

        return $flattened;
    }

    private function extractPathParams($route): array
    {
        preg_match_all('/\{(\w+)\??\}/', $route->uri, $matches);

        return array_map(fn(string $name): string => rtrim($name, '?'), $matches[1]);
    }
}
