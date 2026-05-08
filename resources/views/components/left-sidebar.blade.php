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

    <div class="flex-1 overflow-y-auto">

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
