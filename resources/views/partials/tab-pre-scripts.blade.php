<div x-show="builderTab === 'scripts'">
    <div class="flex items-center justify-between mb-2">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pre-scripts</label>
        <span class="text-[10px] text-gray-400 font-mono">pm.* API</span>
    </div>
    <textarea
        x-model="preScripts"
        rows="4"
        placeholder='pm.sendRequest({ url: pm.variables.get("baseUrl") + "/login", method: "POST" }, function (err, res) { if (err) return; pm.variables.set("token", res.json().access_token); });'
        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-mono text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 outline-none resize-y"
    ></textarea>
    <p class="mt-1 text-[11px] text-gray-400">
        JavaScript runs before each request. Available API:
        <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">pm.sendRequest()</code>,
        <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">pm.variables.get/set()</code>,
        <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">pm.environment.get/set()</code>.
        Use <code class="text-indigo-600 bg-indigo-50 px-0.5 rounded">{<!-- -->{key}<!-- -->}</code> in URL, headers, body to reference variables.
    </p>
</div>
