<div x-show="builderTab === 'query'">
    <div class="flex items-center justify-between mb-2">
        <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Query Params</label>
        <div class="flex items-center gap-2">
            <button
                x-show="queryParams.length > 0"
                @click="resetQueryParams()"
                class="text-[10px] text-gray-500 hover:text-[#a78bfa] transition-colors"
                title="Reset to defaults"
            >↺ reset</button>
            <button
                @click="addQueryParam()"
                class="text-[10px] text-[#667eea] hover:text-[#a78bfa] font-medium transition-colors"
            >+ Add</button>
        </div>
    </div>
    <div class="space-y-1.5">
        <template x-for="(qp, i) in queryParams" :key="i">
            <div class="flex gap-2 items-center">
                <input
                    type="checkbox"
                    x-model="qp.enabled"
                    class="w-3.5 h-3.5 rounded border-[#2a2744] bg-[#1e1c3a] text-[#667eea] focus:ring-[#667eea]/30 shrink-0"
                />
                <input
                    type="text"
                    x-model="qp.key"
                    placeholder="Key"
                    class="flex-1 rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-2.5 py-1.5 text-sm font-mono text-gray-300 placeholder:text-gray-600 focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20 outline-none"
                    :class="{'opacity-40': !qp.enabled}"
                />
                <input
                    type="text"
                    x-model="qp.value"
                    placeholder="Value"
                    class="flex-[2] rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-2.5 py-1.5 text-sm font-mono text-gray-300 placeholder:text-gray-600 focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20 outline-none"
                    :class="{'opacity-40': !qp.enabled}"
                />
                <button
                    @click="removeQueryParam(i)"
                    class="p-1.5 text-gray-500 hover:text-red-400 transition-colors shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </template>
    </div>
</div>