<main class="flex-1 flex flex-col min-w-0">
    <div class="flex-1 overflow-y-auto p-6 space-y-5">

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

        @include('laranova::partials.tab-headers')
        @include('laranova::partials.tab-query-params')
        @include('laranova::partials.tab-body')
        @include('laranova::partials.tab-pre-scripts')

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

        @include('laranova::partials.tab-route-info')
    </div>
</main>
