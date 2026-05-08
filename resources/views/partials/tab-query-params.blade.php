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
