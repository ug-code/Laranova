<main class="flex-1 flex flex-col min-w-0 bg-[#1a1836]/40 backdrop-blur-sm">
    <div class="flex-1 overflow-y-auto p-5 space-y-4">

        <div>
            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Endpoint</label>
            <div class="flex gap-2">
                <select
                    x-model="method"
                    class="w-26 shrink-0 rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-2.5 py-2 text-sm font-bold text-gray-200 shadow-sm focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20 outline-none"
                    :class="methodBgColor(method)"
                >
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="PATCH">PATCH</option>
                    <option value="DELETE">DELETE</option>
                    <option value="HEAD">HEAD</option>
                    <option value="OPTIONS">OPTIONS</option>
                </select>
                <input
                    type="text"
                    x-model="url"
                    placeholder="https://api.example.com/endpoint"
                    class="flex-1 rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-3 py-2 text-sm font-mono text-gray-200 shadow-sm placeholder:text-gray-600 focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20 outline-none"
                />
            </div>
        </div>

        <div class="flex gap-1">
            <button
                @click="builderTab = 'route_info'"
                class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider transition-all border-b-2"
                :class="builderTab === 'route_info' ? 'text-[#a78bfa] border-[#667eea]' : 'text-gray-500 hover:text-gray-300 border-transparent'"
            >Route Info</button>
            <button
                @click="builderTab = 'headers'"
                class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider transition-all border-b-2"
                :class="builderTab === 'headers' ? 'text-[#a78bfa] border-[#667eea]' : 'text-gray-500 hover:text-gray-300 border-transparent'"
            >Headers</button>
            <button
                @click="builderTab = 'query'"
                class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider transition-all border-b-2"
                :class="builderTab === 'query' ? 'text-[#a78bfa] border-[#667eea]' : 'text-gray-500 hover:text-gray-300 border-transparent'"
            >Query Params</button>
            <button
                x-show="['POST', 'PUT', 'PATCH'].includes(method)"
                @click="builderTab = 'body'"
                class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider transition-all border-b-2"
                :class="builderTab === 'body' ? 'text-[#a78bfa] border-[#667eea]' : 'text-gray-500 hover:text-gray-300 border-transparent'"
            >Body</button>
            <button
                @click="builderTab = 'scripts'"
                class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider transition-all border-b-2"
                :class="builderTab === 'scripts' ? 'text-[#a78bfa] border-[#667eea]' : 'text-gray-500 hover:text-gray-300 border-transparent'"
            >Pre-scripts</button>
        </div>

        <div class="min-h-0">
            @include('laranova::partials.tab-route-info')
            @include('laranova::partials.tab-headers')
            @include('laranova::partials.tab-query-params')
            @include('laranova::partials.tab-body')
            @include('laranova::partials.tab-pre-scripts')
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button
                @click="sendRequest()"
                :disabled="loading || !url"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#667eea] to-[#764ba2] px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-[#667eea]/20 hover:shadow-[#667eea]/40 hover:from-[#7b93f5] hover:to-[#8b5fbf] focus:outline-none focus:ring-2 focus:ring-[#667eea]/50 focus:ring-offset-2 focus:ring-offset-[#1a1836] disabled:opacity-40 disabled:cursor-not-allowed transition-all"
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
            <button
                @click="copyAsCurl()"
                :disabled="!url"
                class="inline-flex items-center gap-1.5 rounded-lg border border-[#2a2744] bg-[#1e1c3a]/80 px-3 py-2 text-xs font-mono text-gray-400 hover:text-[#a78bfa] hover:border-[#667eea]/30 hover:bg-[#2a2744]/50 focus:outline-none focus:ring-2 focus:ring-[#667eea]/30 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                &lt;/&gt;&nbsp;cURL
            </button>
            <button
                @click="copyAsPostman()"
                :disabled="!url"
                class="inline-flex items-center gap-1.5 rounded-lg border border-[#2a2744] bg-[#1e1c3a]/80 px-3 py-2 text-xs font-mono text-gray-400 hover:text-[#a78bfa] hover:border-[#667eea]/30 hover:bg-[#2a2744]/50 focus:outline-none focus:ring-2 focus:ring-[#667eea]/30 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                title="Export as Postman Collection v2.1"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Postman
            </button>
        </div>
    </div>
</main>