<div x-show="builderTab === 'body' && ['POST', 'PUT', 'PATCH'].includes(method)">
    <div class="flex items-start gap-2.5 p-2.5 mb-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20" x-show="hasFileUpload">
        <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <div class="text-[11px] text-amber-300">
            <p class="font-semibold">This request requires a file upload.</p>
            <p class="mt-0.5 text-amber-400/70">All fields sent as <span class="font-mono font-semibold">multipart/form-data</span></p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-2">
        <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Body</label>
        <button
            x-show="originalBody"
            @click="resetBody()"
            class="text-[10px] text-gray-500 hover:text-[#a78bfa] transition-colors"
            title="Reset to defaults"
        >↺ reset</button>
    </div>
    <textarea
        x-model="body"
        rows="6"
        placeholder='{"key": "value"}'
        class="w-full rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-3 py-2 text-sm font-mono text-gray-300 placeholder:text-gray-600 focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20 outline-none resize-y"
    ></textarea>

    <div x-show="hasFileUpload && selectedFiles.length > 0" class="mt-3 space-y-2">
        <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Files</label>
        <template x-for="(f, i) in selectedFiles" :key="i">
            <div class="flex gap-2 items-center">
                <input
                    type="text"
                    x-model="f.key"
                    placeholder="field name"
                    class="w-32 rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-2.5 py-1.5 text-sm font-mono text-gray-300 placeholder:text-gray-600 focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20 outline-none"
                />
                <input
                    type="file"
                    @change="f.file = $event.target.files[0]; f.name = $event.target.files[0]?.name || ''"
                    class="flex-1 text-sm text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#667eea]/10 file:text-[#667eea] hover:file:bg-[#667eea]/20"
                />
                <button @click="selectedFiles.splice(i, 1)" class="p-1.5 text-gray-500 hover:text-red-400 transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </template>
        <button @click="selectedFiles.push({ key: '', file: null, name: '' })" class="text-[10px] text-[#667eea] hover:text-[#a78bfa] font-medium transition-colors">+ Add File</button>
    </div>
</div>