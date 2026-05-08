<div x-show="builderTab === 'body' && ['POST', 'PUT', 'PATCH'].includes(method)">
    <div class="flex items-start gap-3 p-3 mb-3 rounded-lg bg-amber-50 border border-amber-200" x-show="hasFileUpload">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <div class="text-xs text-amber-800">
            <p class="font-semibold">This request requires a file upload.</p>
            <p class="mt-0.5 text-amber-700">All fields will be sent as <span class="font-mono font-semibold">multipart/form-data</span></p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-2">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Body</label>
        <button
            x-show="originalBody"
            @click="resetBody()"
            class="text-[11px] text-gray-400 hover:text-indigo-600 transition-colors"
            title="Reset to defaults"
        >↺ reset</button>
    </div>
    <textarea
        x-model="body"
        rows="6"
        placeholder='{"key": "value"}'
        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none resize-y"
    ></textarea>

    <div x-show="hasFileUpload && selectedFiles.length > 0" class="mt-4 space-y-3">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Files</label>
        <template x-for="(f, i) in selectedFiles" :key="i">
            <div class="flex gap-2 items-center">
                <input
                    type="text"
                    x-model="f.key"
                    placeholder="field name"
                    class="w-36 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none"
                />
                <input
                    type="file"
                    @change="f.file = $event.target.files[0]; f.name = $event.target.files[0]?.name || ''"
                    class="flex-1 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                />
                <button @click="selectedFiles.splice(i, 1)" class="p-2 text-gray-400 hover:text-red-500 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </template>
        <button @click="selectedFiles.push({ key: '', file: null, name: '' })" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium transition-colors">+ Add File</button>
    </div>
</div>
