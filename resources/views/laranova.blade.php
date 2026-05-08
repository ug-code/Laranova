<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laranova — API Test Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full overflow-hidden bg-gray-50 font-sans antialiased">
    <div class="flex h-full" x-data="laranova()" x-cloak>
        <style>[x-cloak] { display: none !important; }</style>

        {{-- LEFT: Routes / History --}}
        <aside class="w-72 bg-white border-r border-gray-200 flex flex-col shrink-0">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h1 class="text-lg font-bold text-gray-800 tracking-tight">Laranova</h1>
                <span class="text-[10px] font-mono text-gray-400 uppercase tracking-widest">v1.0</span>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200">
                <button
                    @click="activeTab = 'routes'"
                    class="flex-1 px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors"
                    :class="activeTab === 'routes' ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-500 hover:text-gray-700'"
                >Routes</button>
                <button
                    @click="activeTab = 'history'"
                    class="flex-1 px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors"
                    :class="activeTab === 'history' ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-500 hover:text-gray-700'"
                >History</button>
            </div>

            {{-- Tab content --}}
            <div class="flex-1 overflow-y-auto">

                {{-- === ROUTES TAB === --}}
                <div x-show="activeTab === 'routes'" x-cloak>
                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <span x-text="routeGroups.length"></span> Routes
                        </h2>
                        <button
                            @click="refreshRoutes()"
                            class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                            x-show="!loadingRoutes"
                        >Refresh</button>
                        <span x-show="loadingRoutes" class="text-[11px] text-gray-400">Loading...</span>
                    </div>

                    <template x-if="routeGroups.length === 0 && !loadingRoutes">
                        <div class="px-4 py-8 text-center">
                            <p class="text-sm text-gray-400">No routes found.</p>
                            <p class="text-xs text-gray-300 mt-1">All routes are scanned automatically.</p>
                        </div>
                    </template>

                    <template x-for="group in routeGroups" :key="group.prefix">
                        <div class="border-b border-gray-50">
                            <button
                                @click="toggleRouteGroup(group.prefix)"
                                class="w-full text-left px-4 py-2 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                            >
                                <svg
                                    class="w-3 h-3 text-gray-400 transition-transform"
                                    :class="{'rotate-90': openGroups.includes(group.prefix)}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="text-xs font-mono font-semibold text-gray-600 truncate" x-text="group.prefix || '/'"></span>
                                <span class="text-[10px] text-gray-400 ml-auto" x-text="group.routes.length"></span>
                            </button>

                            <template x-for="route in group.routes" :key="route.uri">
                                <button
                                    @click="loadRoute(route)"
                                    x-show="openGroups.includes(group.prefix)"
                                    class="w-full text-left flex items-center gap-2 px-4 py-2.5 pl-8 hover:bg-gray-50 transition-colors border-t border-gray-50"
                                >
                                    <span
                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold text-white leading-none shrink-0"
                                        :class="methodColor(route.methods[0])"
                                        x-text="route.methods[0]"
                                    ></span>
                                    <span class="text-[11px] font-mono text-gray-600 truncate" x-text="route.uri"></span>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- === HISTORY TAB === --}}
                <div x-show="activeTab === 'history'" x-cloak>
                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">History</h2>
                        <button
                            @click="clearHistory()"
                            x-show="history.length > 0"
                            class="text-[11px] text-red-500 hover:text-red-700 transition-colors"
                        >Clear</button>
                    </div>

                    <template x-if="history.length === 0">
                        <div class="px-4 py-8 text-center">
                            <p class="text-sm text-gray-400">No requests yet.</p>
                            <p class="text-xs text-gray-300 mt-1">Send your first API request to see it here.</p>
                        </div>
                    </template>

                    <template x-for="item in history" :key="item.id">
                        <button
                            @click="loadHistory(item)"
                            class="w-full text-left px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors group"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold text-white leading-none shrink-0"
                                    :class="methodColor(item.method)"
                                    x-text="item.method"
                                ></span>
                                <span
                                    class="text-[11px] font-mono shrink-0"
                                    :class="statusColor(item.status)"
                                    x-text="item.status"
                                ></span>
                                <span class="text-[10px] text-gray-400 ml-auto" x-text="item.duration + 'ms'"></span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1 truncate" x-text="item.url"></p>
                        </button>
                    </template>
                </div>
            </div>

            <div class="px-4 py-2 border-t border-gray-200 text-[10px] text-gray-400 text-center">
                Powered by Laravel HTTP Client
            </div>
        </aside>

        {{-- CENTER: Request Builder --}}
        <main class="flex-1 flex flex-col min-w-0">
            <div class="flex-1 overflow-y-auto p-6 space-y-5">

                {{-- Method & URL --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Endpoint</label>
                    <div class="flex gap-2">
                        <select
                            x-model="method"
                            class="w-28 shrink-0 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-bold text-gray-700 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                            :class="methodBgColor(method)"
                        >
                            <template x-for="m in methods" :key="m">
                                <option x-text="m" :value="m"></option>
                            </template>
                        </select>
                        <input
                            type="text"
                            x-model="url"
                            placeholder="https://api.example.com/endpoint"
                            class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-mono text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                        />
                    </div>
                </div>

                {{-- Headers --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Headers</label>
                        <button
                            @click="addHeader()"
                            class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                        >+ Add</button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(h, i) in headers" :key="i">
                            <div class="flex gap-2 items-center">
                                <input
                                    type="text"
                                    x-model="h.key"
                                    placeholder="Key"
                                    class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                                />
                                <input
                                    type="text"
                                    x-model="h.value"
                                    placeholder="Value"
                                    class="flex-[2] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                                />
                                <button
                                    @click="removeHeader(i)"
                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors shrink-0"
                                    x-show="headers.length > 1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Body --}}
                <div x-show="['POST', 'PUT', 'PATCH'].includes(method)">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Body</label>
                    <textarea
                        x-model="body"
                        rows="6"
                        placeholder='{"key": "value"}'
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none resize-y"
                    ></textarea>
                </div>

                {{-- Pre-scripts --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pre-scripts</label>
                        <span class="text-[10px] text-gray-400 font-mono">@faker:name, @faker:email, ...</span>
                    </div>
                    <textarea
                        x-model="preScripts"
                        rows="3"
                        placeholder='{{ "@faker:name" }} / {{ "@faker:email" }} / {{ "@faker:uuid" }}'
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none resize-y"
                    ></textarea>
                    <p class="mt-1 text-[11px] text-gray-400">
                        Use <code class="text-indigo-600 bg-indigo-50 px-1 rounded">{<!-- -->{ @faker:name }<!-- -->}</code> syntax in URL, headers, body, or here.
                        Supports: name, email, phone, address, city, country, text, sentence, uuid, url, date, company, boolean, etc.
                    </p>
                </div>

                {{-- Send Button --}}
                <div class="flex items-center gap-3 pt-2">
                    <button
                        @click="sendRequest()"
                        :disabled="loading || !url"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <template x-if="loading">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <template x-if="!loading">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Sending...' : 'Send Request'"></span>
                    </button>
                </div>

                {{-- Route Info --}}
                <div x-show="selectedRoute" x-data="{ infoOpen: true }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button
                        @click="infoOpen = !infoOpen"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider"
                    >
                        <span>Route Info</span>
                        <svg
                            class="w-3 h-3 text-gray-400 transition-transform"
                            :class="{'rotate-180': infoOpen}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="infoOpen" class="px-4 py-3 space-y-2 text-xs font-mono">
                        <div x-show="selectedRoute.controller">
                            <span class="text-gray-400">Controller: </span>
                            <span class="text-indigo-600" x-text="selectedRoute.controller + '@' + selectedRoute.action"></span>
                        </div>
                        <div x-show="selectedRoute.middleware && selectedRoute.middleware.length">
                            <span class="text-gray-400">Middleware: </span>
                            <span class="text-gray-600" x-text="selectedRoute.middleware.join(', ')"></span>
                        </div>
                        <div x-show="selectedRoute.pathParams && selectedRoute.pathParams.length">
                            <span class="text-gray-400">Path Params: </span>
                            <span class="text-gray-600" x-text="selectedRoute.pathParams.join(', ')"></span>
                        </div>
                        <div x-show="Object.keys(selectedRoute.rules || {}).length > 0">
                            <span class="text-gray-400 block mb-1">Validation Rules:</span>
                            <div class="space-y-1">
                                <template x-for="(rule, field) in selectedRoute.rules" :key="field">
                                    <div class="flex gap-2">
                                        <span class="text-green-600 shrink-0" x-text="field"></span>
                                        <span class="text-gray-500">:</span>
                                        <span class="text-gray-500 break-all" x-text="rule"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- RIGHT: Response --}}
        <aside class="w-[420px] bg-gray-900 text-gray-100 flex flex-col shrink-0 border-l border-gray-700">
            <div class="px-5 py-3 border-b border-gray-800 flex items-center justify-between shrink-0">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Response</h2>
                <button
                    @click="copyResponse()"
                    x-show="response"
                    class="text-[11px] text-gray-500 hover:text-white transition-colors"
                >Copy</button>
            </div>

            <div class="flex-1 overflow-y-auto">
                {{-- Empty state --}}
                <template x-if="!response">
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center px-8">
                            <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Send a request to see the response here.</p>
                        </div>
                    </div>
                </template>

                <template x-if="response">
                    <div>
                        {{-- Status bar --}}
                        <div class="px-5 py-3 border-b border-gray-800 flex items-center gap-3">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold"
                                :class="statusBadgeColor(response.status)"
                                x-text="response.status || 'Error'"
                            ></span>
                            <span class="text-xs text-gray-400" x-text="response.duration + 'ms'"></span>
                            <span x-show="response.error" class="text-xs text-red-400 ml-auto">Connection Error</span>
                        </div>

                        {{-- Response Headers --}}
                        <div x-data="{ open: false }" class="border-b border-gray-800">
                            <button
                                @click="open = !open"
                                class="w-full px-5 py-2.5 flex items-center justify-between text-xs text-gray-400 hover:text-gray-200 transition-colors"
                            >
                                <span class="font-semibold uppercase tracking-wider">Headers</span>
                                <svg
                                    class="w-3.5 h-3.5 transition-transform"
                                    :class="{'rotate-180': open}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" class="px-5 pb-3 space-y-1">
                                <template x-for="(value, key) in response.headers" :key="key">
                                    <div class="text-[11px] font-mono">
                                        <span class="text-indigo-400" x-text="key"></span>
                                        <span class="text-gray-600">: </span>
                                        <span class="text-gray-300 break-all" x-text="Array.isArray(value) ? value.join(', ') : value"></span>
                                    </div>
                                </template>
                                <template x-if="Object.keys(response.headers).length === 0">
                                    <p class="text-[11px] text-gray-600 italic">No headers</p>
                                </template>
                            </div>
                        </div>

                        {{-- Response Body --}}
                        <div class="px-5 py-3">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Body</h3>
                            </div>
                            <pre
                                class="text-[12px] font-mono leading-relaxed text-gray-200 whitespace-pre-wrap break-all max-h-[60vh] overflow-y-auto"
                                x-text="formatBody(response.body)"
                            ></pre>
                        </div>
                    </div>
                </template>
            </div>
        </aside>
    </div>

    <script>
        function laranova() {
            return {
                // ── State ──
                method: 'GET',
                url: '',
                headers: [{ key: '', value: '' }],
                body: '',
                preScripts: '',
                loading: false,
                response: null,
                history: [],
                methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],

                // ── Routes State ──
                activeTab: 'routes',
                routes: [],
                routeGroups: [],
                openGroups: [],
                loadingRoutes: false,
                selectedRoute: null,

                init() {
                    this.loadHistoryFromSession();
                    this.refreshRoutes();
                },

                // ── Routes ──

                refreshRoutes() {
                    this.loadingRoutes = true;
                    fetch('/laranova/routes')
                        .then(r => r.json())
                        .then(data => {
                            this.routes = data.routes || [];
                            this.routeGroups = this.groupRoutes(this.routes);
                        })
                        .catch(() => {})
                        .finally(() => { this.loadingRoutes = false; });
                },

                groupRoutes(routes) {
                    const groups = {};
                    routes.forEach(route => {
                        const prefix = route.uri.includes('/')
                            ? route.uri.split('/')[0]
                            : '/';
                        if (!groups[prefix]) groups[prefix] = [];
                        groups[prefix].push(route);
                    });
                    return Object.entries(groups).map(([prefix, rs]) => ({
                        prefix: prefix === '/' && rs.length > 1 ? 'root' : prefix,
                        routes: rs,
                    })).sort((a, b) => a.prefix.localeCompare(b.prefix));
                },

                toggleRouteGroup(prefix) {
                    const idx = this.openGroups.indexOf(prefix);
                    if (idx === -1) {
                        this.openGroups.push(prefix);
                    } else {
                        this.openGroups.splice(idx, 1);
                    }
                },

                loadRoute(route) {
                    this.method = route.methods[0];
                    this.url = route.uri;
                    this.selectedRoute = route;

                    if (Object.keys(route.rules || {}).length > 0) {
                        const bodyFields = Object.entries(route.rules)
                            .map(([field, rule]) => `  "${field}": "@{{ @faker:${this.inferFakerType(rule)} }}"`)
                            .join(',\n');
                        if (bodyFields) {
                            this.body = '{\n' + bodyFields + '\n}';
                        }
                    }
                },

                inferFakerType(rule) {
                    if (rule.includes('email')) return 'email';
                    if (rule.includes('url')) return 'url';
                    if (rule.includes('phone')) return 'phone';
                    if (rule.includes('integer') || rule.includes('numeric')) return 'int';
                    if (rule.includes('boolean')) return 'boolean';
                    if (rule.includes('date')) return 'date';
                    if (rule.includes('string') && rule.includes('uuid')) return 'uuid';
                    return 'word';
                },

                // ── History ──

                loadHistoryFromSession() {
                    fetch('/laranova/history')
                        .then(r => r.json())
                        .then(data => { this.history = data.history || []; })
                        .catch(() => {});
                },

                loadHistory(item) {
                    this.method = item.method;
                    this.url = item.url;
                    this.activeTab = 'routes';
                },

                clearHistory() {
                    if (!confirm('Clear all request history?')) return;
                    fetch('/laranova/history', { method: 'DELETE' })
                        .then(r => r.json())
                        .then(() => { this.history = []; })
                        .catch(() => {});
                },

                // ── Headers ──

                addHeader() {
                    this.headers.push({ key: '', value: '' });
                },

                removeHeader(index) {
                    this.headers.splice(index, 1);
                },

                // ── Send Request ──

                async sendRequest() {
                    if (!this.url || this.loading) return;

                    this.loading = true;
                    this.response = null;
                    const startTime = performance.now();

                    try {
                        // Step 1: Resolve faker tags via BE
                        const headers = this.headers.filter(h => h.key.trim() !== '');

                        const resolveResp = await fetch('/laranova/resolve', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: this.url,
                                headers: headers,
                                body: ['POST', 'PUT', 'PATCH'].includes(this.method) ? this.body : '',
                                pre_scripts: this.preScripts,
                            }),
                        });

                        if (!resolveResp.ok) {
                            throw new Error('Faker resolution failed');
                        }

                        const resolved = await resolveResp.json();

                        // Step 2: Send direct request to target API
                        const fetchOptions = {
                            method: resolved.method,
                            headers: { ...resolved.headers },
                        };

                        if (['POST', 'PUT', 'PATCH'].includes(resolved.method) && resolved.body) {
                            fetchOptions.body = resolved.body;
                        }

                        const response = await fetch(resolved.url, fetchOptions);
                        const body = await response.text();

                        const respHeaders = {};
                        response.headers.forEach((value, key) => {
                            respHeaders[key] = value;
                        });

                        this.response = {
                            status: response.status,
                            headers: respHeaders,
                            body: body,
                            duration: Math.round((performance.now() - startTime) * 100) / 100,
                            error: false,
                        };

                        // Step 3: Persist history
                        await fetch('/laranova/history', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: this.url,
                                status: response.status,
                                duration: this.response.duration,
                                error: false,
                            }),
                        });

                        this.loadHistoryFromSession();
                    } catch (err) {
                        this.response = {
                            status: 0,
                            headers: {},
                            body: 'Network error: ' + err.message,
                            duration: Math.round((performance.now() - startTime) * 100) / 100,
                            error: true,
                        };

                        await fetch('/laranova/history', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: this.url,
                                status: 0,
                                duration: this.response.duration,
                                error: true,
                            }),
                        }).catch(() => {});
                    } finally {
                        this.loading = false;
                    }
                },

                // ── Response Formatting ──

                formatBody(body) {
                    if (!body) return '';
                    try {
                        return JSON.stringify(JSON.parse(body), null, 2);
                    } catch {
                        return body;
                    }
                },

                copyResponse() {
                    if (!this.response) return;
                    const text = typeof this.response.body === 'string'
                        ? this.response.body
                        : JSON.stringify(this.response.body, null, 2);
                    navigator.clipboard.writeText(text).catch(() => {});
                },

                // ── Colors ──

                methodColor(m) {
                    const colors = {
                        GET: 'bg-green-500', POST: 'bg-blue-500', PUT: 'bg-orange-500',
                        PATCH: 'bg-purple-500', DELETE: 'bg-red-500', HEAD: 'bg-gray-500', OPTIONS: 'bg-yellow-500',
                    };
                    return colors[m] || 'bg-gray-500';
                },

                methodBgColor(m) {
                    const colors = {
                        GET: 'text-green-700 bg-green-50 border-green-300',
                        POST: 'text-blue-700 bg-blue-50 border-blue-300',
                        PUT: 'text-orange-700 bg-orange-50 border-orange-300',
                        PATCH: 'text-purple-700 bg-purple-50 border-purple-300',
                        DELETE: 'text-red-700 bg-red-50 border-red-300',
                        HEAD: 'text-gray-700 bg-gray-50 border-gray-300',
                        OPTIONS: 'text-yellow-700 bg-yellow-50 border-yellow-300',
                    };
                    return colors[m] || 'text-gray-700 bg-gray-50 border-gray-300';
                },

                statusColor(s) {
                    if (s >= 200 && s < 300) return 'text-green-600';
                    if (s >= 300 && s < 400) return 'text-blue-600';
                    if (s >= 400 && s < 500) return 'text-orange-600';
                    if (s >= 500) return 'text-red-600';
                    return 'text-red-600';
                },

                statusBadgeColor(s) {
                    if (s >= 200 && s < 300) return 'bg-green-600 text-white';
                    if (s >= 300 && s < 400) return 'bg-blue-600 text-white';
                    if (s >= 400 && s < 500) return 'bg-orange-500 text-white';
                    if (s >= 500) return 'bg-red-600 text-white';
                    return 'bg-red-600 text-white';
                },
            };
        }
    </script>
</body>
</html>
