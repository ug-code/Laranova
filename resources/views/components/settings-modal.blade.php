<div x-show="settingsOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-12 pb-8 bg-black/60 backdrop-blur-sm" @click.self="settingsOpen = false">
    <div class="bg-[#15132b] border border-[#2a2744] rounded-xl shadow-2xl shadow-black/50 w-[520px] max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#2a2744]">
            <h2 class="text-sm font-bold text-gray-200">Settings</h2>
            <button @click="settingsOpen = false" class="p-1 text-gray-500 hover:text-[#a78bfa] transition-colors rounded-lg hover:bg-[#2a2744]/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="px-6 py-3.5 border-b border-[#2a2744]/50">
            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Group By</label>
            <select
                x-model="groupBy"
                @change="refreshGrouping()"
                class="w-full rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-3 py-2 text-sm text-gray-300 outline-none focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20"
            >
                <option value="prefix">Route Prefix</option>
                <option value="controller">Controller</option>
                <option value="none">None</option>
            </select>
        </div>

        <div class="px-6 py-3.5 border-b border-[#2a2744]/50">
            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sort By</label>
            <select
                x-model="sortBy"
                @change="refreshGrouping()"
                class="w-full rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-3 py-2 text-sm text-gray-300 outline-none focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20"
            >
                <option value="uri">Route URI</option>
                <option value="name">Route Name</option>
                <option value="method">HTTP Method</option>
                <option value="controller">Controller Name</option>
            </select>
        </div>

        <div class="px-6 py-3.5 border-b border-[#2a2744]/50">
            <div class="flex items-center justify-between mb-2.5">
                <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Variables</label>
                <button
                    @click="newVarKey = ''; newVarVal = ''; variablesOpen = !variablesOpen"
                    class="text-[10px] font-medium text-[#667eea] hover:text-[#a78bfa] transition-colors"
                >+ Add</button>
            </div>
            <div class="space-y-1.5">
                <template x-for="(val, key) in variables" :key="key">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-mono text-gray-400 w-28 shrink-0 truncate" x-text="key"></span>
                        <input
                            type="text"
                            :value="val"
                            @input="updateVariable(key, $event.target.value)"
                            class="flex-1 rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-2.5 py-1.5 text-sm font-mono text-gray-300 outline-none focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20"
                        />
                        <button
                            @click="removeVariable(key)"
                            class="p-1 text-gray-500 hover:text-red-400 transition-colors shrink-0"
                            title="Remove"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <template x-if="variablesOpen">
                <div class="flex items-center gap-2 mt-2.5 pt-2.5 border-t border-[#2a2744]/50">
                    <input
                        x-model="newVarKey"
                        @keydown.enter="saveNewVariable()"
                        placeholder="key"
                        class="w-28 rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-2.5 py-1.5 text-sm font-mono text-gray-300 outline-none focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20"
                    />
                    <input
                        x-model="newVarVal"
                        @keydown.enter="saveNewVariable()"
                        placeholder="value"
                        class="flex-1 rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-2.5 py-1.5 text-sm font-mono text-gray-300 outline-none focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20"
                    />
                    <button
                        @click="saveNewVariable()"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-gradient-to-r from-[#667eea] to-[#764ba2] rounded-lg hover:from-[#7b93f5] hover:to-[#8b5fbf] transition-all"
                    >Add</button>
                </div>
            </template>

            <p class="mt-2.5 text-[10px] text-gray-600">
                Use <code class="text-[#667eea] bg-[#2a2744]/50 px-1 rounded">{<!-- -->{key}<!-- -->}</code> in URL, headers, body.
                Set via <code class="text-[#667eea] bg-[#2a2744]/50 px-1 rounded">pm.variables.set()</code> or <code class="text-[#667eea] bg-[#2a2744]/50 px-1 rounded">pm.environment.set()</code> in pre-scripts.
            </p>
        </div>

        <div class="px-6 py-3.5">
            <button
                @click="clearLocalStorage()"
                class="w-full flex items-center justify-center gap-2 rounded-lg border border-red-500/30 bg-red-500/5 px-4 py-2.5 text-xs font-semibold text-red-400 hover:bg-red-500/15 hover:border-red-500/50 transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Clear LocalStorage &amp; Reset
            </button>
        </div>
    </div>
</div>