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
                <div class="px-5 py-3 border-b border-gray-800 flex items-center gap-3">
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold"
                        :class="statusBadgeColor(response.status)"
                        x-text="response.status || 'Error'"
                    ></span>
                    <span class="text-xs text-gray-400" x-text="response.duration + 'ms'"></span>
                    <span x-show="response.error" class="text-xs text-red-400 ml-auto">Connection Error</span>
                </div>

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
