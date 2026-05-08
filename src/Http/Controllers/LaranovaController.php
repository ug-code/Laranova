<?php

namespace Laranova\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laranova\Services\RouteScanner;
use Laranova\Services\ScriptParser;

class LaranovaController extends Controller
{
    public function __construct(
        private ScriptParser $scriptParser,
        private RouteScanner $routeScanner,
    ) {}

    public function index(): View
    {
        return view('laranova::laranova', [
            'defaultHeaders' => config('laranova.default_headers', []),
            'autoContentType' => config('laranova.auto_content_type', true),
            'security' => config('laranova.security', []),
            'defaultVariables' => config('laranova.default_variables', []),
        ]);
    }

    public function resolve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'method' => 'required|string|in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS',
            'url' => 'required|string',
            'headers' => 'nullable|array',
            'headers.*.key' => 'nullable|string',
            'headers.*.value' => 'nullable|string',
            'body' => 'nullable|string',
            'query_params' => 'nullable|array',
            'query_params.*.key' => 'nullable|string',
            'query_params.*.value' => 'nullable|string',
            'pre_scripts' => 'nullable|string',
        ]);

        $rawHeaders = collect($validated['headers'] ?? [])
            ->filter(fn(array $h): bool => filled($h['key'] ?? null))
            ->pluck('value', 'key')
            ->toArray();

        $rawQueryParams = collect($validated['query_params'] ?? [])
            ->filter(fn(array $qp): bool => filled($qp['key'] ?? null))
            ->map(fn(array $qp): array => [
                'key' => $this->scriptParser->parse($qp['key']),
                'value' => $this->scriptParser->parse($qp['value']),
            ])
            ->values()
            ->toArray();

        $resolved = [
            'method' => strtoupper($validated['method']),
            'url' => $this->scriptParser->parse($validated['url']),
            'headers' => $this->scriptParser->parseArray($rawHeaders),
            'body' => $this->scriptParser->parse($validated['body'] ?? ''),
            'query_params' => $rawQueryParams,
        ];

        if (($validated['pre_scripts'] ?? '') !== '') {
            $this->scriptParser->parse($validated['pre_scripts']);
        }

        return response()->json($resolved);
    }

    public function storeHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'method' => 'required|string',
            'url' => 'required|string',
            'status' => 'required|integer',
            'duration' => 'required|numeric',
            'error' => 'required|boolean',
        ]);

        $this->appendHistory([
            'method' => strtoupper($validated['method']),
            'url' => $validated['url'],
            'status' => $validated['status'],
            'duration' => $validated['duration'],
            'error' => $validated['error'],
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json(['message' => 'History stored.']);
    }

    public function history(): JsonResponse
    {
        return response()->json([
            'history' => Session::get('laranova.history', []),
        ]);
    }

    public function clearHistory(): JsonResponse
    {
        Session::forget('laranova.history');

        return response()->json(['message' => 'History cleared.']);
    }

    public function routes(): JsonResponse
    {
        return response()->json([
            'routes' => $this->routeScanner->scan(),
        ]);
    }

    private function appendHistory(array $item): void
    {
        $history = Session::get('laranova.history', []);

        array_unshift($history, $item);

        $max = config('laranova.history.max_items', 100);

        Session::put('laranova.history', array_slice($history, 0, $max));
    }
}
