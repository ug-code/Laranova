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
                <div class="flex items-center gap-1.5">
                    <button
                        @click="settingsOpen = !settingsOpen"
                        class="p-1 text-gray-400 hover:text-gray-600 transition-colors rounded hover:bg-gray-100"
                        title="Settings"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                    <span class="text-[10px] font-mono text-gray-400 uppercase tracking-widest">v1.0</span>
                </div>
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
                    <div class="px-4 py-2 border-b border-gray-100 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
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

                            <template x-for="route in group.routes" :key="route.methods[0] + '-' + route.uri">
                                <button
                                    @click="loadRoute(route)"
                                    x-show="openGroups.includes(group.prefix)"
                                    :data-route-key="route.methods[0] + '-' + route.uri"
                                    class="w-full text-left px-4 py-2.5 pl-8 hover:bg-gray-50 transition-colors border-t border-gray-50"
                                >
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold text-white leading-none shrink-0"
                                            :class="methodColor(route.methods[0])"
                                            x-text="route.methods[0]"
                                        ></span>
                                        <span class="text-[11px] font-mono text-gray-600 truncate" x-text="route.uri"></span>
                                        <span x-show="route.has_file" class="text-[9px] font-mono text-amber-600 bg-amber-50 px-1 rounded shrink-0">file</span>
                                    </div>
                                    <div x-show="route.middleware && route.middleware.length" class="flex items-center gap-1 mt-1 ml-7">
                                        <template x-for="mw in route.middleware.slice(0, 3)" :key="mw">
                                            <span class="text-[9px] font-mono bg-gray-100 text-gray-500 px-1 rounded truncate max-w-[120px]" x-text="mw.split('\\').pop()"></span>
                                        </template>
                                        <span x-show="route.middleware.length > 3" class="text-[9px] text-gray-400">+<span x-text="route.middleware.length - 3"></span></span>
                                    </div>
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

                {{-- Tabs --}}
                <div class="flex gap-0 border-b border-gray-200">
                    <button
                        @click="builderTab = 'headers'"
                        class="px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wider transition-colors"
                        :class="builderTab === 'headers' ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-400 hover:text-gray-600'"
                    >Headers</button>
                    <button
                        @click="builderTab = 'query'"
                        class="px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wider transition-colors"
                        :class="builderTab === 'query' ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-400 hover:text-gray-600'"
                    >Query Params</button>
                    <button
                        x-show="['POST', 'PUT', 'PATCH'].includes(method)"
                        @click="builderTab = 'body'"
                        class="px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wider transition-colors"
                        :class="builderTab === 'body' ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-400 hover:text-gray-600'"
                    >Body</button>
                    <button
                        @click="builderTab = 'scripts'"
                        class="px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wider transition-colors"
                        :class="builderTab === 'scripts' ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-400 hover:text-gray-600'"
                    >Pre-scripts</button>
                    <button
                        @click="builderTab = 'route_info'"
                        class="px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wider transition-colors"
                        :class="builderTab === 'route_info' ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-400 hover:text-gray-600'"
                    >Route Info</button>
                </div>

                {{-- Headers --}}
                <div x-show="builderTab === 'headers'">
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

                {{-- Query Params --}}
                <div x-show="builderTab === 'query'">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Query Params</label>
                        <div class="flex items-center gap-2">
                            <button
                                x-show="queryParams.length > 0"
                                @click="resetQueryParams()"
                                class="text-[11px] text-gray-400 hover:text-indigo-600 transition-colors"
                                title="Reset to defaults"
                            >↺ reset</button>
                            <button
                                @click="addQueryParam()"
                                class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                            >+ Add</button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(qp, i) in queryParams" :key="i">
                            <div class="flex gap-2 items-center">
                                <input
                                    type="checkbox"
                                    x-model="qp.enabled"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0"
                                />
                                <input
                                    type="text"
                                    x-model="qp.key"
                                    placeholder="Key"
                                    class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                                    :class="{'opacity-40': !qp.enabled}"
                                />
                                <input
                                    type="text"
                                    x-model="qp.value"
                                    placeholder="Value"
                                    class="flex-[2] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                                    :class="{'opacity-40': !qp.enabled}"
                                />
                                <button
                                    @click="removeQueryParam(i)"
                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors shrink-0"
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
                <div x-show="builderTab === 'body' && ['POST', 'PUT', 'PATCH'].includes(method)">
                    <div class="flex items-start gap-3 p-3 mb-3 rounded-lg bg-amber-50 border border-amber-200" x-show="hasFileUpload">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-xs text-amber-800">
                            <p class="font-semibold">This request requires a file upload.</p>
                            <p class="mt-0.5 text-amber-700">All fields will be sent as <span class="font-mono font-semibold">multipart/form-data</span></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Body</label>
                        <button
                            x-show="originalBody"
                            @click="resetBody()"
                            class="text-[11px] text-gray-400 hover:text-indigo-600 transition-colors"
                            title="Reset to defaults"
                        >↺ reset</button>
                    </div>
                    <textarea
                        x-model="body"
                        rows="6"
                        placeholder='{"key": "value"}'
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none resize-y"
                    ></textarea>

                    <div x-show="hasFileUpload && selectedFiles.length > 0" class="mt-4 space-y-3">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Files</label>
                        <template x-for="(f, i) in selectedFiles" :key="i">
                            <div class="flex gap-2 items-center">
                                <input
                                    type="text"
                                    x-model="f.key"
                                    placeholder="field name"
                                    class="w-36 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                                />
                                <input
                                    type="file"
                                    @change="f.file = $event.target.files[0]; f.name = $event.target.files[0]?.name || ''"
                                    class="flex-1 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <button @click="selectedFiles.splice(i, 1)" class="p-2 text-gray-400 hover:text-red-500 transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <button @click="selectedFiles.push({ key: '', file: null, name: '' })" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium transition-colors">+ Add File</button>
                    </div>
                </div>

                {{-- Pre-scripts --}}
                <div x-show="builderTab === 'scripts'">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pre-scripts</label>
                        <span class="text-[10px] text-gray-400 font-mono">pm.* API</span>
                    </div>
                    <textarea
                        x-model="preScripts"
                        rows="4"
                        placeholder='pm.sendRequest({ url: pm.variables.get("baseUrl") + "/login", method: "POST" }, function (err, res) { if (err) return; pm.variables.set("token", res.json().access_token); });'
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none resize-y"
                    ></textarea>
                    <p class="mt-1 text-[11px] text-gray-400">
                        JavaScript runs before each request. Available API:
                        <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">pm.sendRequest()</code>,
                        <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">pm.variables.get/set()</code>,
                        <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">pm.environment.get/set()</code>.
                        Use <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">{<!-- -->{key}<!-- -->}</code> in URL, headers, body to reference variables.
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

                {{-- Route Info (Tab) --}}
                <div x-show="builderTab === 'route_info'" x-cloak>
                    <template x-if="!selectedRoute">
                        <div class="text-center py-12">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <p class="text-sm text-gray-400">Select a route to view details.</p>
                        </div>
                    </template>
                    <template x-if="selectedRoute">
                        <div class="space-y-4 text-xs font-mono">
                            <div>
                                <span class="text-gray-400">Controller: </span>
                                <span class="text-indigo-600 font-semibold" x-text="selectedRoute.controller + '@' + selectedRoute.action"></span>
                            </div>
                            <div x-show="selectedRoute.middleware && selectedRoute.middleware.length">
                                <span class="text-gray-400 block mb-1.5">Middleware:</span>
                                <div class="space-y-1">
                                    <template x-for="mw in selectedRoute.middleware" :key="mw">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                                            <span class="text-gray-600 break-all text-[11px]" x-text="mw"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div x-show="selectedRoute.pathParams && selectedRoute.pathParams.length">
                                <span class="text-gray-400">Path Params: </span>
                                <span class="text-gray-600" x-text="selectedRoute.pathParams.join(', ')"></span>
                            </div>
                            <div x-show="Object.keys(selectedRoute.rules || {}).length > 0">
                                <span class="text-gray-400 block mb-1.5">Validation Rules:</span>
                                <div class="space-y-1.5">
                                    <template x-for="(rule, field) in selectedRoute.rules" :key="field">
                                        <div class="flex gap-2">
                                            <span class="text-green-600 shrink-0 font-semibold" x-text="field"></span>
                                            <span class="text-gray-500">:</span>
                                            <span class="text-gray-500 break-all" x-text="rule"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
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

                        {{-- Sent Payload --}}
                        <div x-data="{ open: false }" class="border-b border-gray-800">
                            <button
                                @click="open = !open"
                                class="w-full px-5 py-2.5 flex items-center justify-between text-xs text-gray-400 hover:text-gray-200 transition-colors"
                            >
                                <span class="font-semibold uppercase tracking-wider">Payload</span>
                                <svg
                                    class="w-3.5 h-3.5 transition-transform"
                                    :class="{'rotate-180': open}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" class="px-5 pb-3 space-y-2">
                                <div class="text-[11px] font-mono">
                                    <span class="text-gray-500">Method: </span>
                                    <span class="text-white" x-text="sentPayload.method"></span>
                                </div>
                                <div class="text-[11px] font-mono break-all">
                                    <span class="text-gray-500">URL: </span>
                                    <span class="text-gray-300" x-text="sentPayload.url"></span>
                                </div>
                                <div x-show="Object.keys(sentPayload.headers).length > 0">
                                    <span class="text-[11px] text-gray-500 font-mono block mb-1">Headers:</span>
                                    <div class="space-y-0.5 ml-2">
                                        <template x-for="(value, key) in sentPayload.headers" :key="key">
                                            <div class="text-[11px] font-mono">
                                                <span class="text-indigo-400" x-text="key"></span>
                                                <span class="text-gray-600">: </span>
                                                <span class="text-gray-300 break-all" x-text="value"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="sentPayload.body">
                                    <span class="text-[11px] text-gray-500 font-mono block mb-1">Body:</span>
                                    <pre class="text-[11px] font-mono leading-relaxed text-gray-300 whitespace-pre-wrap break-all ml-2" x-text="sentPayload.body"></pre>
                                </div>
                            </div>
                        </div>

                        {{-- Response Body --}}
                        <div class="px-5 py-3">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Body</h3>
                            </div>
                            <pre
                                class="text-[12px] font-mono leading-relaxed whitespace-pre-wrap break-all max-h-[60vh] overflow-y-auto"
                                x-html="formatBody(response.body)"
                            ></pre>
                        </div>
                    </div>
                </template>
            </div>
        </aside>

        {{-- Settings Modal --}}
        <div x-show="settingsOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-12 pb-8 bg-black/40" @click.self="settingsOpen = false">
            <div class="bg-white rounded-xl shadow-2xl w-[520px] max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-bold text-gray-800">Settings</h2>
                    <button @click="settingsOpen = false" class="p-1 text-gray-400 hover:text-gray-600 transition-colors rounded hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Group By --}}
                <div class="px-6 py-4 border-b border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Group By</label>
                    <select
                        x-model="groupBy"
                        @change="refreshGrouping()"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    >
                        <option value="prefix">Route Prefix</option>
                        <option value="controller">Controller</option>
                        <option value="none">None</option>
                    </select>
                </div>

                {{-- Sort By --}}
                <div class="px-6 py-4 border-b border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sort By</label>
                    <select
                        x-model="sortBy"
                        @change="refreshGrouping()"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    >
                        <option value="uri">Route URI</option>
                        <option value="name">Route Name</option>
                        <option value="method">HTTP Method</option>
                        <option value="controller">Controller Name</option>
                    </select>
                </div>

                {{-- Variables --}}
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Variables</label>
                        <button
                            @click="newVarKey = ''; newVarVal = ''; variablesOpen = !variablesOpen"
                            class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                        >+ Add</button>
                    </div>
                    <div class="space-y-1.5">
                        <template x-for="(val, key) in variables" :key="key">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-mono text-gray-700 w-28 shrink-0 truncate" x-text="key"></span>
                                <input
                                    type="text"
                                    :value="val"
                                    @input="updateVariable(key, $event.target.value)"
                                    class="flex-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm font-mono text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                                />
                                <button
                                    @click="removeVariable(key)"
                                    class="p-1 text-gray-300 hover:text-red-500 transition-colors shrink-0"
                                    title="Remove"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Add new variable form --}}
                    <template x-if="variablesOpen">
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                            <input
                                x-model="newVarKey"
                                @keydown.enter="saveNewVariable()"
                                placeholder="key"
                                class="w-28 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm font-mono text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                            />
                            <input
                                x-model="newVarVal"
                                @keydown.enter="saveNewVariable()"
                                placeholder="value"
                                class="flex-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm font-mono text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                            />
                            <button
                                @click="saveNewVariable()"
                                class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
                            >Add</button>
                        </div>
                    </template>

                    <p class="mt-3 text-[11px] text-gray-400">
                        Use <code class="text-indigo-500 bg-indigo-50 px-1 rounded">{<!-- -->{key}<!-- -->}</code> in URL, headers, body.
                        Set via <code class="text-indigo-500 bg-indigo-50 px-1 rounded">pm.variables.set()</code> or <code class="text-indigo-500 bg-indigo-50 px-1 rounded">pm.environment.set()</code> in pre-scripts.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function laranova() {
            return {
                // ── State ──
                method: 'GET',
                url: '',
                headers: [{ key: '', value: '' }],
                queryParams: [],
                originalQueryParams: [],
                originalBody: '',
                body: '',
                hasFileUpload: false,
                selectedFiles: [],
                preScripts: '',
                loading: false,
                response: null,
                sentPayload: null,
                history: [],
                methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],

                // ── UI State ──
                settingsOpen: false,
                builderTab: 'headers',

                // ── Variables ──
                variables: {},
                newVarKey: '',
                newVarVal: '',
                variablesOpen: false,

                // ── Routes State ──
                activeTab: 'routes',
                routes: [],
                routeGroups: [],
                openGroups: [],
                loadingRoutes: false,
                selectedRoute: null,
                groupBy: 'prefix',
                sortBy: 'uri',

                init() {
                    // ── Load persisted settings ──
                    const savedGroupBy = localStorage.getItem('laranova_groupBy');
                    if (savedGroupBy) this.groupBy = savedGroupBy;
                    const savedSortBy = localStorage.getItem('laranova_sortBy');
                    if (savedSortBy) this.sortBy = savedSortBy;

                    // ── Restore last endpoint ──
                    const savedMethod = localStorage.getItem('laranova_method');
                    const savedUrl = localStorage.getItem('laranova_url');
                    if (savedMethod) this.method = savedMethod;
                    if (savedUrl) this.url = savedUrl;

                    this.$watch('groupBy', val => localStorage.setItem('laranova_groupBy', val));
                    this.$watch('sortBy', val => localStorage.setItem('laranova_sortBy', val));
                    this.$watch('method', val => localStorage.setItem('laranova_method', val));
                    this.$watch('url', val => localStorage.setItem('laranova_url', val));

                    // ── Variables: config defaults + localStorage ──
                    const savedVars = localStorage.getItem('laranova_variables');
                    const defaultVars = @json($defaultVariables);
                    const removedVars = JSON.parse(localStorage.getItem('laranova_removed_vars') || '[]');
                    if (savedVars) {
                        try {
                            const parsed = JSON.parse(savedVars);
                            this.variables = { ...defaultVars, ...parsed };
                        } catch { this.variables = { ...defaultVars }; }
                    } else {
                        this.variables = { ...defaultVars };
                    }
                    removedVars.forEach(k => { delete this.variables[k]; });
                    this.$watch('variables', val => {
                        localStorage.setItem('laranova_variables', JSON.stringify(val));
                    });

                    // ── Headers: localStorage or config defaults ──
                    const savedHeaders = localStorage.getItem('laranova_headers');
                    if (savedHeaders) {
                        try { this.headers = JSON.parse(savedHeaders); } catch {}
                    } else {
                        const defaults = @json($defaultHeaders);
                        this.headers = Object.keys(defaults).length > 0
                            ? Object.entries(defaults).map(([k, v]) => ({ key: k, value: v }))
                            : [{ key: '', value: '' }];

                        const sec = @json($security);
                        if (sec.type === 'bearer' && !this.headers.some(h => h.key.toLowerCase() === 'authorization')) {
                            this.headers.unshift({ key: 'Authorization', value: 'Bearer ' });
                        }
                    }
                    this.$watch('headers', val => {
                        localStorage.setItem('laranova_headers', JSON.stringify(val));
                    }, { deep: true });

                    // ── Query Params: localStorage ──
                    const savedQueryParams = localStorage.getItem('laranova_queryParams');
                    if (savedQueryParams) {
                        try { this.queryParams = JSON.parse(savedQueryParams); } catch {}
                    }
                    this.$watch('queryParams', val => {
                        localStorage.setItem('laranova_queryParams', JSON.stringify(val));
                    }, { deep: true });

                    // ── Pre-scripts: localStorage ──
                    const savedPre = localStorage.getItem('laranova_preScripts');
                    if (savedPre) this.preScripts = savedPre;
                    this.$watch('preScripts', val => {
                        localStorage.setItem('laranova_preScripts', val);
                    });

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
                            this.buildGroups();
                            this.restoreLastRoute();
                        })
                        .catch(() => {})
                        .finally(() => { this.loadingRoutes = false; });
                },

                restoreLastRoute() {
                    const savedMethod = localStorage.getItem('laranova_method');
                    const savedUrl = localStorage.getItem('laranova_url');
                    if (!savedMethod || !savedUrl) return;

                    for (const group of this.routeGroups) {
                        const route = group.routes.find(r =>
                            r.uri === savedUrl && (r.methods || []).includes(savedMethod)
                        );
                        if (route) {
                            if (!this.openGroups.includes(group.prefix)) {
                                this.openGroups.push(group.prefix);
                            }
                            this.loadRoute(route);
                            this.method = savedMethod;
                            this.url = savedUrl;
                            localStorage.setItem('laranova_method', savedMethod);
                            localStorage.setItem('laranova_url', savedUrl);
                            setTimeout(() => {
                                const key = savedMethod + '-' + savedUrl;
                                const el = document.querySelector(`[data-route-key="${key}"]`);
                                if (el) el.scrollIntoView({ block: 'center', behavior: 'smooth' });
                            }, 50);
                            return;
                        }
                    }

                    // Route not found — still ensure method/url from localStorage stick
                    this.method = savedMethod;
                    this.url = savedUrl;
                    localStorage.setItem('laranova_method', savedMethod);
                    localStorage.setItem('laranova_url', savedUrl);
                },

                refreshGrouping() {
                    this.buildGroups();
                },

                buildGroups() {
                    const routes = this.routes;
                    if (routes.length === 0) {
                        this.routeGroups = [];
                        return;
                    }

                    const groupMap = {};

                    routes.forEach(route => {
                        let key;
                        if (this.groupBy === 'controller') {
                            key = route.controller || 'Unknown';
                        } else if (this.groupBy === 'none') {
                            key = 'All Routes';
                        } else {
                            key = route.uri.includes('/') ? route.uri.split('/')[0] : '/';
                            if (key === '/' && routes.filter(r => r.uri === '/' || !r.uri.includes('/')).length > 1) {
                                key = 'root';
                            }
                        }
                        if (!groupMap[key]) groupMap[key] = [];
                        groupMap[key].push(route);
                    });

                    this.routeGroups = Object.entries(groupMap)
                        .map(([prefix, rs]) => ({
                            prefix,
                            routes: rs.sort((a, b) => this.routeSorter(a, b)),
                        }))
                        .sort((a, b) => a.prefix.localeCompare(b.prefix));

                    this.openGroups = this.groupBy === 'controller'
                        ? this.routeGroups.map(g => g.prefix)
                        : [];
                },

                routeSorter(a, b) {
                    if (this.sortBy === 'name') {
                        return (a.name || '').localeCompare(b.name || '');
                    }
                    if (this.sortBy === 'method') {
                        return a.methods[0].localeCompare(b.methods[0]);
                    }
                    if (this.sortBy === 'controller') {
                        return (a.controller || '').localeCompare(b.controller || '');
                    }
                    return a.uri.localeCompare(b.uri);
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
                    this.queryParams = [];
                    this.selectedFiles = [];
                    this.hasFileUpload = false;

                    const isBodyMethod = ['POST', 'PUT', 'PATCH'].includes(this.method);
                    const rules = route.rules || {};

                    this.hasFileUpload = route.has_file || false;

                    if (Object.keys(rules).length > 0) {
                        if (isBodyMethod) {
                            // Split rules into body fields and file fields
                            const entries = Object.entries(rules);
                            const fileFields = entries.filter(([, rule]) =>
                                rule.includes('file') || rule.includes('image')
                            );
                            const bodyOnlyFields = entries.filter(([, rule]) =>
                                !(rule.includes('file') || rule.includes('image'))
                            );

                            // Pre-populate file inputs for file/image fields
                            this.selectedFiles = fileFields.map(([field]) => {
                                const parts = field.split('.');
                                const key = parts.length > 1
                                    ? parts[0] + parts.slice(1).map(p => `[${p}]`).join('')
                                    : field;
                                return { key, file: null, name: '' };
                            });

                            // Build body JSON from non-file fields only
                            if (bodyOnlyFields.length > 0) {
                                this.body = '{\n' + bodyOnlyFields
                                    .map(([field, rule]) => `  "${field}": "${this.inferFakerValue(rule, field)}"`)
                                    .join(',\n') + '\n}';
                            } else {
                                this.body = '';
                            }
                            this.originalBody = this.body;
                            this.originalQueryParams = [];
                        } else {
                            this.body = '';
                            this.originalBody = '';
                            const entries = Object.entries(rules);
                            const parentKeys = new Set(
                                entries
                                    .filter(([field]) => field.includes('.'))
                                    .map(([field]) => field.split('.')[0])
                            );

                            this.queryParams = entries
                                .filter(([field]) => !parentKeys.has(field))
                                .map(([field, rule]) => {
                                    const parts = field.split('.');
                                    const key = parts.length > 1
                                        ? parts[0] + parts.slice(1).map(p => `[${p}]`).join('')
                                        : field;
                                    return { key, value: this.inferFakerValue(rule, field), enabled: true };
                                });
                            this.originalQueryParams = JSON.parse(JSON.stringify(this.queryParams));
                        }
                    } else {
                        this.originalBody = '';
                        this.originalQueryParams = [];
                    }
                },

                inferFakerValue(rule, fieldName = '') {
                    const inMatch = rule.match(/in:([^|]+)/);
                    if (inMatch) {
                        const values = inMatch[1].split(',');
                        return values[Math.floor(Math.random() * values.length)];
                    }
                    if (rule.includes('boolean')) return Math.random() > 0.5 ? '1' : '0';
                    const name = fieldName.split('.').pop();
                    if (name.endsWith('_from')) return '@{{ @faker:date_from }}';
                    if (name.endsWith('_to')) return '@{{ @faker:date_to }}';
                    if (rule.includes('email')) return '@{{ @faker:email }}';
                    if (rule.includes('url')) return '@{{ @faker:url }}';
                    if (rule.includes('phone')) return '@{{ @faker:phone }}';
                    if (rule.includes('integer') || rule.includes('numeric')) return '@{{ @faker:int }}';
                    if (rule.includes('date')) return '@{{ @faker:date }}';
                    if (rule.includes('string') && rule.includes('uuid')) return '@{{ @faker:uuid }}';
                    if (rule.includes('array')) return '{}';
                    return '@{{ @faker:word }}';
                },

                // ── Variables ──

                addVariable(key, val) {
                    this.variables = { ...this.variables, [key]: val };
                },

                updateVariable(key, value) {
                    this.variables = { ...this.variables, [key]: value };
                },

                removeVariable(key) {
                    const newVars = {};
                    Object.keys(this.variables).forEach(k => {
                        if (k !== key) newVars[k] = this.variables[k];
                    });
                    this.variables = newVars;
                    const removed = JSON.parse(localStorage.getItem('laranova_removed_vars') || '[]');
                    if (!removed.includes(key)) {
                        removed.push(key);
                        localStorage.setItem('laranova_removed_vars', JSON.stringify(removed));
                    }
                },

                saveNewVariable() {
                    const key = this.newVarKey.trim();
                    const val = this.newVarVal.trim();
                    if (key) {
                        this.variables = { ...this.variables, [key]: val };
                        this.newVarKey = '';
                        this.newVarVal = '';
                        this.variablesOpen = false;
                    }
                },

                replaceVariables(str) {
                    if (!str || typeof str !== 'string') return str;
                    return str.replace(/\x7B\x7B(\w+)\x7D\x7D/g, (match, key) => {
                        if (this.variables[key] !== undefined) return this.variables[key];
                        const envVal = localStorage.getItem('laranova_env_' + key);
                        if (envVal !== null) return envVal;
                        return match;
                    });
                },

                // ── Pre-scripts (Postman Compat) ──

                buildPostmanAPI() {
                    const self = this;
                    const _pending = [];

                    const api = {
                        _pending,
                        variables: {
                            get: (key) => self.variables[key],
                            set: (key, val) => {
                                self.variables = { ...self.variables, [key]: val };
                            },
                            unset: (key) => { self.removeVariable(key); },
                            clear: () => { self.variables = {}; },
                            replace: (str) => self.replaceVariables(str),
                        },
                        environment: {
                            get: (key) => localStorage.getItem('laranova_env_' + key),
                            set: (key, val) => localStorage.setItem('laranova_env_' + key, val),
                            unset: (key) => localStorage.removeItem('laranova_env_' + key),
                            clear: () => {
                                const keys = Object.keys(localStorage).filter(k => k.startsWith('laranova_env_'));
                                keys.forEach(k => localStorage.removeItem(k));
                            },
                        },
                        sendRequest: (config, callback) => {
                            const promise = (async () => {
                                const url = api.variables.replace(config.url || '');
                                const options = {
                                    method: config.method || 'GET',
                                    headers: config.header || config.headers || {},
                                };
                                if (config.body?.raw) {
                                    options.body = config.body.raw;
                                } else if (typeof config.body === 'string') {
                                    options.body = config.body;
                                }
                                const resp = await fetch(url, options);
                                const text = await resp.text();
                                const result = {
                                    code: resp.status,
                                    status: resp.status,
                                    statusCode: resp.status,
                                    headers: Object.fromEntries(resp.headers.entries()),
                                    json: () => { try { return JSON.parse(text); } catch { return {}; } },
                                    text: () => text,
                                };
                                return result;
                            })();

                            if (callback) {
                                _pending.push(
                                    promise.then(res => callback(null, res)).catch(err => callback(err, null))
                                );
                            }

                            return promise;
                        },
                    };

                    return api;
                },

                async execPreScript(code) {
                    if (!code || !code.trim()) return;

                    const pm = this.buildPostmanAPI();

                    const wrappedCode = `
                        return (async () => {
                            ${code}
                        })();
                    `;

                    try {
                        const fn = new Function('pm', wrappedCode);
                        await fn(pm);
                        await Promise.all(pm._pending);
                    } catch (e) {
                        throw new Error('Pre-script: ' + e.message);
                    }
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

                addQueryParam() {
                    this.queryParams.push({ key: '', value: '', enabled: true });
                },

                removeQueryParam(index) {
                    this.queryParams.splice(index, 1);
                },

                resetQueryParams() {
                    this.queryParams = JSON.parse(JSON.stringify(this.originalQueryParams));
                },

                resetBody() {
                    this.body = this.originalBody;
                },

                // ── Send Request ──

                async sendRequest() {
                    if (!this.url || this.loading) return;

                    this.loading = true;
                    this.response = null;
                    const startTime = performance.now();

                    try {
                        // Step 0: Run pre-scripts (Postman-compatible JS)
                        try {
                            await this.execPreScript(this.preScripts);
                        } catch (e) {
                            this.response = {
                                status: 0,
                                headers: {},
                                body: e.message,
                                duration: 0,
                                error: true,
                            };
                            this.loading = false;
                            return;
                        }

                        // Step 1: Replace template variables in URL, headers, body, query params
                        const rawHeaders = this.headers.filter(h => h.key.trim() !== '');
                        const rawQueryParams = this.queryParams.filter(qp => qp.key.trim() !== '' && qp.enabled !== false);
                        const resolvedUrl = this.replaceVariables(this.url);
                        const resolvedBody = this.replaceVariables(
                            ['POST', 'PUT', 'PATCH'].includes(this.method) ? this.body : ''
                        );
                        const resolvedHeaders = rawHeaders.map(h => ({
                            key: this.replaceVariables(h.key),
                            value: this.replaceVariables(h.value),
                        }));
                        const resolvedQueryParams = rawQueryParams.map(qp => ({
                            key: this.replaceVariables(qp.key),
                            value: this.replaceVariables(qp.value),
                        }));

                        const hasFiles = this.selectedFiles.some(f => f.file);

                        // Step 2: Resolve faker tags via BE
                        const resolveResp = await fetch('/laranova/resolve', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: resolvedUrl,
                                headers: resolvedHeaders,
                                body: resolvedBody,
                                query_params: resolvedQueryParams,
                                pre_scripts: '',
                            }),
                        });

                        if (!resolveResp.ok) {
                            throw new Error('Faker resolution failed');
                        }

                        const fakerResolved = await resolveResp.json();

                        // Build URL with query params
                        let finalUrl = fakerResolved.url;
                        if (fakerResolved.query_params && fakerResolved.query_params.length > 0) {
                            const searchParams = new URLSearchParams();
                            fakerResolved.query_params.forEach(qp => {
                                if (qp.key) searchParams.append(qp.key, qp.value);
                            });
                            const qs = searchParams.toString();
                            if (qs) finalUrl += (finalUrl.includes('?') ? '&' : '?') + qs;
                        }

                        // Step 3: Send direct request to target API
                        let fetchOptions;
                        let debugBody;
                        if (hasFiles) {
                            const formData = new FormData();
                            const toFormKey = (k) => {
                                const parts = k.split('.');
                                return parts.length > 1
                                    ? parts[0] + parts.slice(1).map(p => `[${p}]`).join('')
                                    : k;
                            };
                            // Parse resolved body JSON and append each field to FormData
                            if (fakerResolved.body) {
                                try {
                                    const parsedBody = JSON.parse(fakerResolved.body);
                                    Object.entries(parsedBody).forEach(([k, v]) => {
                                        if (v !== null && v !== undefined) {
                                            formData.append(toFormKey(k), String(v));
                                        }
                                    });
                                } catch {}
                            }
                            // Append files (overwrites any body field with same key)
                            this.selectedFiles.forEach(f => {
                                if (f.file) formData.append(f.key, f.file);
                            });
                            fetchOptions = {
                                method: fakerResolved.method,
                                headers: Object.fromEntries(
                                    Object.entries(fakerResolved.headers).filter(
                                        ([k]) => k.toLowerCase() !== 'content-type'
                                    )
                                ),
                                body: formData,
                            };
                            debugBody = '[FormData]';
                        } else {
                            fetchOptions = {
                                method: fakerResolved.method,
                                headers: { ...fakerResolved.headers },
                            };
                            if (['POST', 'PUT', 'PATCH'].includes(fakerResolved.method) && fakerResolved.body) {
                                fetchOptions.body = fakerResolved.body;
                                debugBody = fakerResolved.body;
                                if (@json($autoContentType)) {
                                    const hasContentType = Object.keys(fetchOptions.headers).some(
                                        h => h.toLowerCase() === 'content-type'
                                    );
                                    if (!hasContentType) {
                                        fetchOptions.headers['Content-Type'] = 'application/json';
                                    }
                                }
                            } else {
                                debugBody = '';
                            }
                        }

                        // Save sent payload for Payload tab
                        const sentHeaders = { ...fetchOptions.headers };
                        if (hasFiles) {
                            sentHeaders['Content-Type'] = 'multipart/form-data';
                        }
                        this.sentPayload = {
                            method: fakerResolved.method,
                            url: finalUrl,
                            headers: sentHeaders,
                            body: debugBody,
                        };

                        const response = await fetch(finalUrl, fetchOptions);
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

                        // Step 4: Persist history
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
                            body: 'Error: ' + err.message,
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

                            this.loadHistoryFromSession();
                        } finally {
                        this.loading = false;
                    }
                },

                // ── Response Formatting ──

                formatBody(body) {
                    if (!body) return '';
                    try {
                        const parsed = JSON.parse(body);
                        const json = JSON.stringify(parsed, null, 2);
                        return json.replace(
                            /("(?:\\.|[^"\\])*")\s*:/g,
                            '<span class="text-amber-300">$1</span>:'
                        ).replace(
                            /:\s*("(?:\\.|[^"\\])*")/g,
                            ': <span class="text-green-300">$1</span>'
                        ).replace(
                            /:\s*(true|false)/g,
                            ': <span class="text-sky-300">$1</span>'
                        ).replace(
                            /:\s*(null)/g,
                            ': <span class="text-gray-400">$1</span>'
                        ).replace(
                            /:\s*(-?\d+\.?\d*(?:e[+-]?\d+)?)/g,
                            ': <span class="text-purple-300">$1</span>'
                        );
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
