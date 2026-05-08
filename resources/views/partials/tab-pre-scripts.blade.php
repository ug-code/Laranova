<div x-show="builderTab === 'scripts'">
    <div class="flex items-center justify-between mb-2">
        <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Pre-scripts</label>
        <span class="text-[9px] text-gray-600 font-mono">pm.* API</span>
    </div>
    <textarea
        x-model="preScripts"
        rows="4"
        placeholder='pm.sendRequest({ url: pm.variables.get("baseUrl") + "/login", method: "POST" }, function (err, res) { if (err) return; pm.variables.set("token", res.json().access_token); });'
        class="w-full rounded-lg border border-[#2a2744] bg-[#1e1c3a] px-3 py-2 text-sm font-mono text-gray-300 placeholder:text-gray-600 focus:border-[#667eea] focus:ring-2 focus:ring-[#667eea]/20 outline-none resize-y"
    ></textarea>
    <p class="mt-1 text-[10px] text-gray-600">
        JavaScript runs before each request. Available API:
        <code class="text-[#667eea] bg-[#2a2744]/50 px-0.5 rounded">pm.sendRequest()</code>,
        <code class="text-[#667eea] bg-[#2a2744]/50 px-0.5 rounded">pm.variables.get/set()</code>,
        <code class="text-[#667eea] bg-[#2a2744]/50 px-0.5 rounded">pm.environment.get/set()</code>.
        Use <code class="text-[#667eea] bg-[#2a2744]/50 px-0.5 rounded">{<!-- -->{key}<!-- -->}</code> in URL, headers, body to reference variables.
    </p>
</div>