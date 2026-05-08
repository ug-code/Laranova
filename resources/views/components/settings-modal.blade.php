<div x-show="settingsOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-12 pb-8 bg-black/40" @click.self="settingsOpen = false">
    <div class="bg-white rounded-xl shadow-2xl w-[520px] max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-sm font-bold text-gray-800">Settings</h2>
            <button @click="settingsOpen = false" class="p-1 text-gray-400 hover:text-gray-600 transition-colors rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="px-6 py-4 border-b border-gray-100">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Group By</label>
            <select
                x-model="groupBy"
                @change="refreshGrouping()"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
            >
                <option value="prefix">Route Prefix</option>
                <option value="controller">Controller</option>
                <option value="none">None</option>
            </select>
        </div>

        <div class="px-6 py-4 border-b border-gray-100">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sort By</label>
            <select
                x-model="sortBy"
                @change="refreshGrouping()"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
            >
                <option value="uri">Route URI</option>
                <option value="name">Route Name</option>
                <option value="method">HTTP Method</option>
                <option value="controller">Controller Name</option>
            </select>
        </div>

        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-3">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Variables</label>
                <button
                    @click="newVarKey = ''; newVarVal = ''; variablesOpen = !variablesOpen"
                    class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                >+ Add</button>
            </div>
            <div class="space-y-1.5">
                <template x-for="(val, key) in variables" :key="key">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-mono text-gray-700 w-28 shrink-0 truncate" x-text="key"></span>
                        <input
                            type="text"
                            :value="val"
                            @input="updateVariable(key, $event.target.value)"
                            class="flex-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm font-mono text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                        />
                        <button
                            @click="removeVariable(key)"
                            class="p-1 text-gray-300 hover:text-red-500 transition-colors shrink-0"
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
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                    <input
                        x-model="newVarKey"
                        @keydown.enter="saveNewVariable()"
                        placeholder="key"
                        class="w-28 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm font-mono text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    />
                    <input
                        x-model="newVarVal"
                        @keydown.enter="saveNewVariable()"
                        placeholder="value"
                        class="flex-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm font-mono text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    />
                    <button
                        @click="saveNewVariable()"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
                    >Add</button>
                </div>
            </template>

            <p class="mt-3 text-[11px] text-gray-400">
                Use <code class="text-indigo-500 bg-indigo-50 px-1 rounded">{<!-- -->{key}<!-- -->}</code> in URL, headers, body.
                Set via <code class="text-indigo-500 bg-indigo-50 px-1 rounded">pm.variables.set()</code> or <code class="text-indigo-500 bg-indigo-50 px-1 rounded">pm.environment.set()</code> in pre-scripts.
            </p>
        </div>
    </div>
</div>
