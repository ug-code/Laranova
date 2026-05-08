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
