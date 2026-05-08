<aside class="w-72 bg-[#15132b]/90 backdrop-blur-md border-r border-[#2a2744] flex flex-col shrink-0">
    <div class="px-4 py-2.5 border-b border-[#2a2744] flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h1 class="text-base font-bold bg-gradient-to-r from-[#a78bfa] to-[#667eea] bg-clip-text text-transparent tracking-tight">Laranova</h1>
        </div>
        <div class="flex items-center gap-1.5">
            <button
                @click="settingsOpen = !settingsOpen"
                class="p-1.5 text-gray-500 hover:text-[#a78bfa] transition-colors rounded-lg hover:bg-[#2a2744]/50"
                title="Settings"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
            <span class="text-[9px] font-mono text-gray-600 uppercase tracking-widest">v1.0</span>
        </div>
    </div>

    <div class="flex border-b border-[#2a2744] bg-[#1a1836]/40">
        <button
            @click="activeTab = 'routes'"
            class="flex-1 px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-all"
            :class="activeTab === 'routes' ? 'text-[#a78bfa] border-b-2 border-[#667eea]' : 'text-gray-500 hover:text-gray-300'"
        >Routes</button>
        <button
            @click="activeTab = 'history'"
            class="flex-1 px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-all"
            :class="activeTab === 'history' ? 'text-[#a78bfa] border-b-2 border-[#667eea]' : 'text-gray-500 hover:text-gray-300'"
        >History</button>
    </div>

    <div class="flex-1 overflow-y-auto">
        <div x-show="activeTab === 'routes'" x-cloak>
            <div class="px-4 py-1.5 border-b border-[#2a2744]/50 flex items-center justify-between">
                <h2 class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                    <span x-text="routeGroups.length"></span> Routes
                </h2>
                <button
                    @click="refreshRoutes()"
                    class="text-[10px] text-[#667eea] hover:text-[#a78bfa] font-medium transition-colors"
                    x-show="!loadingRoutes"
                >Refresh</button>
                <span x-show="loadingRoutes" class="text-[10px] text-gray-500">Loading...</span>
            </div>

            <template x-if="routeGroups.length === 0 && !loadingRoutes">
                <div class="px-4 py-10 text-center">
                    <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-sm text-gray-500">No routes found.</p>
                    <p class="text-xs text-gray-600 mt-1">Routes scanned automatically.</p>
                </div>
            </template>

            <template x-for="group in routeGroups" :key="group.prefix">
                <div>
                    <button
                        @click="toggleRouteGroup(group.prefix)"
                        class="w-full text-left px-4 py-1.5 flex items-center gap-2 hover:bg-[#2a2744]/30 transition-colors"
                    >
                        <svg
                            class="w-2.5 h-2.5 text-gray-500 transition-transform"
                            :class="{'rotate-90': openGroups.includes(group.prefix)}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-[11px] font-mono font-semibold text-gray-400 truncate" x-text="group.prefix || '/'"></span>
                        <span class="text-[9px] text-gray-600 ml-auto" x-text="group.routes.length"></span>
                    </button>

                    <template x-for="route in group.routes" :key="route.methods[0] + '-' + route.uri">
                        <button
                            @click="loadRoute(route)"
                            x-show="openGroups.includes(group.prefix)"
                            :data-route-key="route.methods[0] + '-' + route.uri"
                            class="w-full text-left pl-8 pr-3 py-1.5 hover:bg-[#2a2744]/30 transition-colors border-t border-[#2a2744]/20"
                        >
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span
                                    class="inline-flex items-center px-1 py-0.5 rounded text-[9px] font-bold text-white leading-none shrink-0"
                                    :class="methodColor(route.methods[0])"
                                    x-text="route.methods[0]"
                                ></span>
                                <span class="text-[10px] font-mono text-gray-400 truncate" x-text="route.uri"></span>
                                <span x-show="route.has_file" class="text-[8px] font-mono text-amber-400 bg-amber-400/10 px-1 rounded shrink-0">file</span>
                            </div>
                            <div x-show="route.middleware && route.middleware.length" class="flex items-center gap-1 mt-0.5 ml-0">
                                <template x-for="mw in route.middleware.slice(0, 3)" :key="mw">
                                    <span class="text-[8px] font-mono bg-[#2a2744]/50 text-gray-500 px-1 rounded truncate max-w-[100px]" x-text="mw.split('\\').pop()"></span>
                                </template>
                                <span x-show="route.middleware.length > 3" class="text-[8px] text-gray-600">+<span x-text="route.middleware.length - 3"></span></span>
                            </div>
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <div x-show="activeTab === 'history'" x-cloak>
            <div class="px-4 py-1.5 border-b border-[#2a2744]/50 flex items-center justify-between">
                <h2 class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">History</h2>
                <button
                    @click="clearHistory()"
                    x-show="history.length > 0"
                    class="text-[10px] text-red-400 hover:text-red-300 transition-colors"
                >Clear</button>
            </div>

            <template x-if="history.length === 0">
                <div class="px-4 py-10 text-center">
                    <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-gray-500">No requests yet.</p>
                    <p class="text-xs text-gray-600 mt-1">Send your first request to see it here.</p>
                </div>
            </template>

            <template x-for="item in history" :key="item.id">
                <button
                    @click="loadHistory(item)"
                    class="w-full text-left px-4 py-2 border-b border-[#2a2744]/30 hover:bg-[#2a2744]/30 transition-colors group"
                >
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center px-1 py-0.5 rounded text-[9px] font-bold text-white leading-none shrink-0"
                            :class="methodColor(item.method)"
                            x-text="item.method"
                        ></span>
                        <span
                            class="text-[10px] font-mono shrink-0"
                            :class="statusColor(item.status)"
                            x-text="item.status"
                        ></span>
                        <span class="text-[9px] text-gray-600 ml-auto" x-text="item.duration + 'ms'"></span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-0.5 truncate" x-text="item.url"></p>
                </button>
            </template>
        </div>
    </div>

    <div class="px-4 py-1.5 border-t border-[#2a2744] text-[9px] text-gray-600 text-center bg-[#1a1836]/40">
        Powered by Laravel HTTP Client
    </div>
</aside>