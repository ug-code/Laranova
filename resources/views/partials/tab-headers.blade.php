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
