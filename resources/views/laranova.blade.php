<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laranova — API Test Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full overflow-hidden bg-gray-50 font-sans antialiased">
    <div class="flex h-full" x-data="laranova()" x-cloak>
        <style>[x-cloak] { display: none !important; }</style>

        @include('laranova::components.left-sidebar')
        @include('laranova::components.center-panel')
        @include('laranova::components.right-panel')
        @include('laranova::components.settings-modal')
    </div>

    <script>
        function laranova() {
            return {
                // ── State ──
                method: 'GET',
                url: '',
                headers: [{ key: '', value: '' }],
                queryParams: [],
                originalQueryParams: [],
                originalBody: '',
                body: '',
                hasFileUpload: false,
                selectedFiles: [],
                preScripts: '',
                loading: false,
                response: null,
                sentPayload: null,
                history: [],
                methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],

                // ── UI State ──
                settingsOpen: false,
                builderTab: 'headers',

                // ── Variables ──
                variables: {},
                newVarKey: '',
                newVarVal: '',
                variablesOpen: false,

                // ── Routes State ──
                activeTab: 'routes',
                routes: [],
                routeGroups: [],
                openGroups: [],
                loadingRoutes: false,
                selectedRoute: null,
                groupBy: 'prefix',
                sortBy: 'uri',

                init() {
                    // ── Load persisted settings ──
                    const savedGroupBy = localStorage.getItem('laranova_groupBy');
                    if (savedGroupBy) this.groupBy = savedGroupBy;
                    const savedSortBy = localStorage.getItem('laranova_sortBy');
                    if (savedSortBy) this.sortBy = savedSortBy;

                    // ── Restore last endpoint ──
                    const savedMethod = localStorage.getItem('laranova_method');
                    const savedUrl = localStorage.getItem('laranova_url');
                    if (savedMethod) this.method = savedMethod;
                    if (savedUrl) this.url = savedUrl;

                    this.$watch('groupBy', val => localStorage.setItem('laranova_groupBy', val));
                    this.$watch('sortBy', val => localStorage.setItem('laranova_sortBy', val));
                    this.$watch('method', val => localStorage.setItem('laranova_method', val));
                    this.$watch('url', val => localStorage.setItem('laranova_url', val));

                    // ── Variables: config defaults + localStorage ──
                    const savedVars = localStorage.getItem('laranova_variables');
                    const defaultVars = @json($defaultVariables);
                    const removedVars = JSON.parse(localStorage.getItem('laranova_removed_vars') || '[]');
                    if (savedVars) {
                        try {
                            const parsed = JSON.parse(savedVars);
                            this.variables = { ...defaultVars, ...parsed };
                        } catch { this.variables = { ...defaultVars }; }
                    } else {
                        this.variables = { ...defaultVars };
                    }
                    removedVars.forEach(k => { delete this.variables[k]; });
                    this.$watch('variables', val => {
                        localStorage.setItem('laranova_variables', JSON.stringify(val));
                    });

                    // ── Headers: localStorage or config defaults ──
                    const savedHeaders = localStorage.getItem('laranova_headers');
                    if (savedHeaders) {
                        try { this.headers = JSON.parse(savedHeaders); } catch {}
                    } else {
                        const defaults = @json($defaultHeaders);
                        this.headers = Object.keys(defaults).length > 0
                            ? Object.entries(defaults).map(([k, v]) => ({ key: k, value: v }))
                            : [{ key: '', value: '' }];

                        const sec = @json($security);
                        if (sec.type === 'bearer' && !this.headers.some(h => h.key.toLowerCase() === 'authorization')) {
                            this.headers.unshift({ key: 'Authorization', value: 'Bearer ' });
                        }
                    }
                    this.$watch('headers', val => {
                        localStorage.setItem('laranova_headers', JSON.stringify(val));
                    }, { deep: true });

                    // ── Query Params: localStorage ──
                    const savedQueryParams = localStorage.getItem('laranova_queryParams');
                    if (savedQueryParams) {
                        try { this.queryParams = JSON.parse(savedQueryParams); } catch {}
                    }
                    this.$watch('queryParams', val => {
                        localStorage.setItem('laranova_queryParams', JSON.stringify(val));
                    }, { deep: true });

                    // ── Pre-scripts: localStorage ──
                    const savedPre = localStorage.getItem('laranova_preScripts');
                    if (savedPre) this.preScripts = savedPre;
                    this.$watch('preScripts', val => {
                        localStorage.setItem('laranova_preScripts', val);
                    });

                    this.loadHistoryFromSession();
                    this.refreshRoutes();
                },

                // ── Routes ──

                refreshRoutes() {
                    this.loadingRoutes = true;
                    fetch('/laranova/routes')
                        .then(r => r.json())
                        .then(data => {
                            this.routes = data.routes || [];
                            this.buildGroups();
                            this.restoreLastRoute();
                        })
                        .catch(() => {})
                        .finally(() => { this.loadingRoutes = false; });
                },

                restoreLastRoute() {
                    const savedMethod = localStorage.getItem('laranova_method');
                    const savedUrl = localStorage.getItem('laranova_url');
                    if (!savedMethod || !savedUrl) return;

                    for (const group of this.routeGroups) {
                        const route = group.routes.find(r =>
                            r.uri === savedUrl && (r.methods || []).includes(savedMethod)
                        );
                        if (route) {
                            if (!this.openGroups.includes(group.prefix)) {
                                this.openGroups.push(group.prefix);
                            }
                            this.loadRoute(route);
                            this.method = savedMethod;
                            this.url = savedUrl;
                            localStorage.setItem('laranova_method', savedMethod);
                            localStorage.setItem('laranova_url', savedUrl);
                            setTimeout(() => {
                                const key = savedMethod + '-' + savedUrl;
                                const el = document.querySelector(`[data-route-key="${key}"]`);
                                if (el) el.scrollIntoView({ block: 'center', behavior: 'smooth' });
                            }, 50);
                            return;
                        }
                    }

                    this.method = savedMethod;
                    this.url = savedUrl;
                    localStorage.setItem('laranova_method', savedMethod);
                    localStorage.setItem('laranova_url', savedUrl);
                },

                refreshGrouping() {
                    this.buildGroups();
                },

                buildGroups() {
                    const routes = this.routes;
                    if (routes.length === 0) {
                        this.routeGroups = [];
                        return;
                    }

                    const groupMap = {};

                    routes.forEach(route => {
                        let key;
                        if (this.groupBy === 'controller') {
                            key = route.controller || 'Unknown';
                        } else if (this.groupBy === 'none') {
                            key = 'All Routes';
                        } else {
                            key = route.uri.includes('/') ? route.uri.split('/')[0] : '/';
                            if (key === '/' && routes.filter(r => r.uri === '/' || !r.uri.includes('/')).length > 1) {
                                key = 'root';
                            }
                        }
                        if (!groupMap[key]) groupMap[key] = [];
                        groupMap[key].push(route);
                    });

                    this.routeGroups = Object.entries(groupMap)
                        .map(([prefix, rs]) => ({
                            prefix,
                            routes: rs.sort((a, b) => this.routeSorter(a, b)),
                        }))
                        .sort((a, b) => a.prefix.localeCompare(b.prefix));

                    this.openGroups = this.groupBy === 'controller'
                        ? this.routeGroups.map(g => g.prefix)
                        : [];
                },

                routeSorter(a, b) {
                    if (this.sortBy === 'name') {
                        return (a.name || '').localeCompare(b.name || '');
                    }
                    if (this.sortBy === 'method') {
                        return a.methods[0].localeCompare(b.methods[0]);
                    }
                    if (this.sortBy === 'controller') {
                        return (a.controller || '').localeCompare(b.controller || '');
                    }
                    return a.uri.localeCompare(b.uri);
                },

                toggleRouteGroup(prefix) {
                    const idx = this.openGroups.indexOf(prefix);
                    if (idx === -1) {
                        this.openGroups.push(prefix);
                    } else {
                        this.openGroups.splice(idx, 1);
                    }
                },

                loadRoute(route) {
                    this.method = route.methods[0];
                    this.url = route.uri;
                    this.selectedRoute = route;
                    this.queryParams = [];
                    this.selectedFiles = [];
                    this.hasFileUpload = false;

                    const isBodyMethod = ['POST', 'PUT', 'PATCH'].includes(this.method);
                    const rules = route.rules || {};

                    this.hasFileUpload = route.has_file || false;

                    if (Object.keys(rules).length > 0) {
                        if (isBodyMethod) {
                            const entries = Object.entries(rules);
                            const fileFields = entries.filter(([, rule]) =>
                                rule.includes('file') || rule.includes('image')
                            );
                            const bodyOnlyFields = entries.filter(([field, rule]) =>
                                !(rule.includes('file') || rule.includes('image')) && !field.endsWith('.*')
                            );

                            this.selectedFiles = fileFields.map(([field]) => {
                                const parts = field.split('.');
                                const key = parts.length > 1
                                    ? parts[0] + parts.slice(1).map(p => `[${p}]`).join('')
                                    : field;
                                return { key, file: null, name: '' };
                            });

                            if (bodyOnlyFields.length > 0) {
                                this.body = JSON.stringify(this.buildBodyObject(bodyOnlyFields), null, 2);
                            } else {
                                this.body = '';
                            }
                            this.originalBody = this.body;
                            this.originalQueryParams = [];
                        } else {
                            this.body = '';
                            this.originalBody = '';
                            const entries = Object.entries(rules);
                            const parentKeys = new Set(
                                entries
                                    .filter(([field]) => field.includes('.'))
                                    .map(([field]) => field.split('.')[0])
                            );

                            this.queryParams = entries
                                .filter(([field]) => !parentKeys.has(field))
                                .map(([field, rule]) => {
                                    const parts = field.split('.');
                                    const key = parts.length > 1
                                        ? parts[0] + parts.slice(1).map(p => `[${p}]`).join('')
                                        : field;
                                    return { key, value: this.inferFakerValue(rule, field), enabled: true };
                                });
                            this.originalQueryParams = JSON.parse(JSON.stringify(this.queryParams));
                        }
                    } else {
                        this.originalBody = '';
                        this.originalQueryParams = [];
                    }
                },

                inferFakerValue(rule, fieldName = '') {
                    const inMatch = rule.match(/in:([^|]+)/);
                    if (inMatch) {
                        const values = inMatch[1].split(',');
                        return values[Math.floor(Math.random() * values.length)];
                    }
                    if (rule.includes('boolean')) return Math.random() > 0.5 ? '1' : '0';
                    const name = fieldName.split('.').pop();
                    if (name.endsWith('_from')) return '@{{ @faker:date_from }}';
                    if (name.endsWith('_to')) return '@{{ @faker:date_to }}';
                    if (name.endsWith('_id')) return '@{{ @faker:int }}';
                    if (rule.includes('email')) return '@{{ @faker:email }}';
                    if (rule.includes('url')) return '@{{ @faker:url }}';
                    if (rule.includes('phone')) return '@{{ @faker:phone }}';
                    if (rule.includes('integer') || rule.includes('numeric')) return '@{{ @faker:int }}';
                    if (rule.includes('date')) return '@{{ @faker:date }}';
                    if (rule.includes('string') && rule.includes('uuid')) return '@{{ @faker:uuid }}';
                    if (rule.includes('array')) return '[]';
                    return '@{{ @faker:word }}';
                },

                buildBodyObject(entries) {
                    const result = {};
                    const objArrays = new Map();
                    const children = new Map();
                    const processed = new Set();

                    entries.forEach(([field, rule]) => {
                        const parts = field.split('.');
                        if (parts.length === 3 && parts[1] === '*') {
                            const parent = parts[0];
                            const prop = parts[2];
                            if (!objArrays.has(parent)) objArrays.set(parent, []);
                            objArrays.get(parent).push([prop, rule]);
                            processed.add(field);
                        } else if (field.endsWith('.*')) {
                            processed.add(field);
                        } else if (parts.length === 2) {
                            const parent = parts[0];
                            const child = parts[1];
                            if (!children.has(parent)) children.set(parent, []);
                            children.get(parent).push([child, rule]);
                            processed.add(field);
                        }
                    });

                    entries.forEach(([field, rule]) => {
                        if (processed.has(field)) return;

                        if (objArrays.has(field)) {
                            const item = {};
                            objArrays.get(field).forEach(([prop, propRule]) => {
                                item[prop] = this.inferFakerTypedValue(propRule, prop);
                            });
                            result[field] = [item];
                        } else if (children.has(field)) {
                            const obj = {};
                            children.get(field).forEach(([child, childRule]) => {
                                if (childRule.includes('array')) {
                                    obj[child] = [];
                                } else {
                                    obj[child] = this.inferFakerTypedValue(childRule, child);
                                }
                            });
                            result[field] = obj;
                        } else if (rule.includes('array')) {
                            result[field] = [];
                        } else {
                            result[field] = this.inferFakerTypedValue(rule, field);
                        }
                    });

                    children.forEach((childList, parent) => {
                        if (!(parent in result)) {
                            const obj = {};
                            childList.forEach(([child, childRule]) => {
                                if (childRule.includes('array')) {
                                    obj[child] = [];
                                } else {
                                    obj[child] = this.inferFakerTypedValue(childRule, child);
                                }
                            });
                            result[parent] = obj;
                        }
                    });

                    return result;
                },

                inferFakerTypedValue(rule, fieldName = '') {
                    const inMatch = rule.match(/in:([^|]+)/);
                    if (inMatch) {
                        const values = inMatch[1].split(',');
                        return values[Math.floor(Math.random() * values.length)];
                    }
                    if (rule.includes('boolean')) return Math.random() > 0.5 ? 1 : 0;
                    const name = fieldName.split('.').pop();
                    if (name.endsWith('_from')) return '@{{ @faker:date_from }}';
                    if (name.endsWith('_to')) return '@{{ @faker:date_to }}';
                    if (name.endsWith('_id')) return 1;
                    if (rule.includes('email')) return '@{{ @faker:email }}';
                    if (rule.includes('url')) return '@{{ @faker:url }}';
                    if (rule.includes('phone')) return '@{{ @faker:phone }}';
                    if (rule.includes('integer') || rule.includes('numeric')) return 1;
                    if (rule.includes('date')) return '@{{ @faker:date }}';
                    if (rule.includes('string') && rule.includes('uuid')) return '@{{ @faker:uuid }}';
                    return '@{{ @faker:word }}';
                },

                // ── Variables ──

                addVariable(key, val) {
                    this.variables = { ...this.variables, [key]: val };
                },

                updateVariable(key, value) {
                    this.variables = { ...this.variables, [key]: value };
                },

                removeVariable(key) {
                    const newVars = {};
                    Object.keys(this.variables).forEach(k => {
                        if (k !== key) newVars[k] = this.variables[k];
                    });
                    this.variables = newVars;
                    const removed = JSON.parse(localStorage.getItem('laranova_removed_vars') || '[]');
                    if (!removed.includes(key)) {
                        removed.push(key);
                        localStorage.setItem('laranova_removed_vars', JSON.stringify(removed));
                    }
                },

                saveNewVariable() {
                    const key = this.newVarKey.trim();
                    const val = this.newVarVal.trim();
                    if (key) {
                        this.variables = { ...this.variables, [key]: val };
                        this.newVarKey = '';
                        this.newVarVal = '';
                        this.variablesOpen = false;
                    }
                },

                replaceVariables(str) {
                    if (!str || typeof str !== 'string') return str;
                    return str.replace(/\x7B\x7B(\w+)\x7D\x7D/g, (match, key) => {
                        if (this.variables[key] !== undefined) return this.variables[key];
                        const envVal = localStorage.getItem('laranova_env_' + key);
                        if (envVal !== null) return envVal;
                        return match;
                    });
                },

                // ── Pre-scripts (Postman Compat) ──

                buildPostmanAPI() {
                    const self = this;
                    const _pending = [];

                    const api = {
                        _pending,
                        variables: {
                            get: (key) => self.variables[key],
                            set: (key, val) => {
                                self.variables = { ...self.variables, [key]: val };
                            },
                            unset: (key) => { self.removeVariable(key); },
                            clear: () => { self.variables = {}; },
                            replace: (str) => self.replaceVariables(str),
                        },
                        environment: {
                            get: (key) => localStorage.getItem('laranova_env_' + key),
                            set: (key, val) => localStorage.setItem('laranova_env_' + key, val),
                            unset: (key) => localStorage.removeItem('laranova_env_' + key),
                            clear: () => {
                                const keys = Object.keys(localStorage).filter(k => k.startsWith('laranova_env_'));
                                keys.forEach(k => localStorage.removeItem(k));
                            },
                        },
                        sendRequest: (config, callback) => {
                            const promise = (async () => {
                                const url = api.variables.replace(config.url || '');
                                const options = {
                                    method: config.method || 'GET',
                                    headers: config.header || config.headers || {},
                                };
                                if (config.body?.raw) {
                                    options.body = config.body.raw;
                                } else if (typeof config.body === 'string') {
                                    options.body = config.body;
                                }
                                const resp = await fetch(url, options);
                                const text = await resp.text();
                                const result = {
                                    code: resp.status,
                                    status: resp.status,
                                    statusCode: resp.status,
                                    headers: Object.fromEntries(resp.headers.entries()),
                                    json: () => { try { return JSON.parse(text); } catch { return {}; } },
                                    text: () => text,
                                };
                                return result;
                            })();

                            if (callback) {
                                _pending.push(
                                    promise.then(res => callback(null, res)).catch(err => callback(err, null))
                                );
                            }

                            return promise;
                        },
                    };

                    return api;
                },

                async execPreScript(code) {
                    if (!code || !code.trim()) return;

                    const pm = this.buildPostmanAPI();

                    const wrappedCode = `
                        return (async () => {
                            ${code}
                        })();
                    `;

                    try {
                        const fn = new Function('pm', wrappedCode);
                        await fn(pm);
                        await Promise.all(pm._pending);
                    } catch (e) {
                        throw new Error('Pre-script: ' + e.message);
                    }
                },

                // ── History ──

                loadHistoryFromSession() {
                    fetch('/laranova/history')
                        .then(r => r.json())
                        .then(data => { this.history = data.history || []; })
                        .catch(() => {});
                },

                loadHistory(item) {
                    this.method = item.method;
                    this.url = item.url;
                    this.activeTab = 'routes';
                },

                clearHistory() {
                    if (!confirm('Clear all request history?')) return;
                    fetch('/laranova/history', { method: 'DELETE' })
                        .then(r => r.json())
                        .then(() => { this.history = []; })
                        .catch(() => {});
                },

                // ── Headers ──

                addHeader() {
                    this.headers.push({ key: '', value: '' });
                },

                removeHeader(index) {
                    this.headers.splice(index, 1);
                },

                addQueryParam() {
                    this.queryParams.push({ key: '', value: '', enabled: true });
                },

                removeQueryParam(index) {
                    this.queryParams.splice(index, 1);
                },

                resetQueryParams() {
                    this.queryParams = JSON.parse(JSON.stringify(this.originalQueryParams));
                },

                resetBody() {
                    this.body = this.originalBody;
                },

                // ── Send Request ──

                async sendRequest() {
                    if (!this.url || this.loading) return;

                    this.loading = true;
                    this.response = null;
                    const startTime = performance.now();

                    try {
                        try {
                            await this.execPreScript(this.preScripts);
                        } catch (e) {
                            this.response = {
                                status: 0,
                                headers: {},
                                body: e.message,
                                duration: 0,
                                error: true,
                            };
                            this.loading = false;
                            return;
                        }

                        const rawHeaders = this.headers.filter(h => h.key.trim() !== '');
                        const rawQueryParams = this.queryParams.filter(qp => qp.key.trim() !== '' && qp.enabled !== false);
                        const resolvedUrl = this.replaceVariables(this.url);
                        const resolvedBody = this.replaceVariables(
                            ['POST', 'PUT', 'PATCH'].includes(this.method) ? this.body : ''
                        );
                        const resolvedHeaders = rawHeaders.map(h => ({
                            key: this.replaceVariables(h.key),
                            value: this.replaceVariables(h.value),
                        }));
                        const resolvedQueryParams = rawQueryParams.map(qp => ({
                            key: this.replaceVariables(qp.key),
                            value: this.replaceVariables(qp.value),
                        }));

                        const hasFiles = this.selectedFiles.some(f => f.file);

                        const resolveResp = await fetch('/laranova/resolve', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: resolvedUrl,
                                headers: resolvedHeaders,
                                body: resolvedBody,
                                query_params: resolvedQueryParams,
                                pre_scripts: '',
                            }),
                        });

                        if (!resolveResp.ok) {
                            throw new Error('Faker resolution failed');
                        }

                        const fakerResolved = await resolveResp.json();

                        let finalUrl = fakerResolved.url;
                        if (fakerResolved.query_params && fakerResolved.query_params.length > 0) {
                            const searchParams = new URLSearchParams();
                            fakerResolved.query_params.forEach(qp => {
                                if (qp.key) searchParams.append(qp.key, qp.value);
                            });
                            const qs = searchParams.toString();
                            if (qs) finalUrl += (finalUrl.includes('?') ? '&' : '?') + qs;
                        }

                        let fetchOptions;
                        let debugBody;
                        if (hasFiles) {
                            const formData = new FormData();
                            const toFormKey = (k) => {
                                const parts = k.split('.');
                                return parts.length > 1
                                    ? parts[0] + parts.slice(1).map(p => `[${p}]`).join('')
                                    : k;
                            };
                            if (fakerResolved.body) {
                                try {
                                    const parsedBody = JSON.parse(fakerResolved.body);
                                    Object.entries(parsedBody).forEach(([k, v]) => {
                                        if (v !== null && v !== undefined) {
                                            formData.append(toFormKey(k), String(v));
                                        }
                                    });
                                } catch {}
                            }
                            this.selectedFiles.forEach(f => {
                                if (f.file) formData.append(f.key, f.file);
                            });
                            fetchOptions = {
                                method: fakerResolved.method,
                                headers: Object.fromEntries(
                                    Object.entries(fakerResolved.headers).filter(
                                        ([k]) => k.toLowerCase() !== 'content-type'
                                    )
                                ),
                                body: formData,
                            };
                            debugBody = '[FormData]';
                        } else {
                            fetchOptions = {
                                method: fakerResolved.method,
                                headers: { ...fakerResolved.headers },
                            };
                            if (['POST', 'PUT', 'PATCH'].includes(fakerResolved.method) && fakerResolved.body) {
                                fetchOptions.body = fakerResolved.body;
                                debugBody = fakerResolved.body;
                                if (@json($autoContentType)) {
                                    const hasContentType = Object.keys(fetchOptions.headers).some(
                                        h => h.toLowerCase() === 'content-type'
                                    );
                                    if (!hasContentType) {
                                        fetchOptions.headers['Content-Type'] = 'application/json';
                                    }
                                }
                            } else {
                                debugBody = '';
                            }
                        }

                        const sentHeaders = { ...fetchOptions.headers };
                        if (hasFiles) {
                            sentHeaders['Content-Type'] = 'multipart/form-data';
                        }
                        this.sentPayload = {
                            method: fakerResolved.method,
                            url: finalUrl,
                            headers: sentHeaders,
                            body: debugBody,
                        };

                        const response = await fetch(finalUrl, fetchOptions);
                        const body = await response.text();

                        const respHeaders = {};
                        response.headers.forEach((value, key) => {
                            respHeaders[key] = value;
                        });

                        this.response = {
                            status: response.status,
                            headers: respHeaders,
                            body: body,
                            duration: Math.round((performance.now() - startTime) * 100) / 100,
                            error: false,
                        };

                        await fetch('/laranova/history', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: this.url,
                                status: response.status,
                                duration: this.response.duration,
                                error: false,
                            }),
                        });

                        this.loadHistoryFromSession();
                    } catch (err) {
                        this.response = {
                            status: 0,
                            headers: {},
                            body: 'Error: ' + err.message,
                            duration: Math.round((performance.now() - startTime) * 100) / 100,
                            error: true,
                        };

                        await fetch('/laranova/history', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: this.url,
                                status: 0,
                                duration: this.response.duration,
                                error: true,
                            }),
                        }).catch(() => {});

                        this.loadHistoryFromSession();
                    } finally {
                        this.loading = false;
                    }
                },

                // ── Response Formatting ──

                formatBody(body) {
                    if (!body) return '';
                    try {
                        const parsed = JSON.parse(body);
                        const json = JSON.stringify(parsed, null, 2);
                        return json.replace(
                            /("(?:\\.|[^"\\])*")\s*:/g,
                            '<span class="text-amber-300">$1</span>:'
                        ).replace(
                            /:\s*("(?:\\.|[^"\\])*")/g,
                            ': <span class="text-green-300">$1</span>'
                        ).replace(
                            /:\s*(true|false)/g,
                            ': <span class="text-sky-300">$1</span>'
                        ).replace(
                            /:\s*(null)/g,
                            ': <span class="text-gray-400">$1</span>'
                        ).replace(
                            /:\s*(-?\d+\.?\d*(?:e[+-]?\d+)?)/g,
                            ': <span class="text-purple-300">$1</span>'
                        );
                    } catch {
                        return body;
                    }
                },

                copyToClipboard(text) {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text).catch(() => this.copyFallback(text));
                    } else {
                        this.copyFallback(text);
                    }
                },

                copyFallback(text) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); } catch {}
                    document.body.removeChild(ta);
                },

                copyResponse() {
                    if (!this.response) return;
                    const text = typeof this.response.body === 'string'
                        ? this.response.body
                        : JSON.stringify(this.response.body, null, 2);
                    this.copyToClipboard(text);
                },

                async copyAsCurl() {
                    if (!this.url) return;

                    const rawHeaders = this.headers.filter(h => h.key.trim() !== '');
                    const rawQueryParams = this.queryParams.filter(qp => qp.key.trim() !== '' && qp.enabled !== false);
                    const resolvedUrl = this.replaceVariables(this.url);
                    const resolvedBody = this.replaceVariables(
                        ['POST', 'PUT', 'PATCH'].includes(this.method) ? this.body : ''
                    );
                    const resolvedHeaders = rawHeaders.map(h => ({
                        key: this.replaceVariables(h.key),
                        value: this.replaceVariables(h.value),
                    }));
                    const resolvedQueryParams = rawQueryParams.map(qp => ({
                        key: this.replaceVariables(qp.key),
                        value: this.replaceVariables(qp.value),
                    }));

                    let fakerResolved;
                    try {
                        const resp = await fetch('/laranova/resolve', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                method: this.method,
                                url: resolvedUrl,
                                headers: resolvedHeaders,
                                body: resolvedBody,
                                query_params: resolvedQueryParams,
                                pre_scripts: '',
                            }),
                        });
                        if (!resp.ok) throw new Error('resolve failed');
                        fakerResolved = await resp.json();
                    } catch {
                        // fallback: use var-resolved values as-is (faker tags remain unresolved)
                        fakerResolved = {
                            method: this.method,
                            url: resolvedUrl,
                            headers: Object.fromEntries(resolvedHeaders.map(h => [h.key, h.value])),
                            body: resolvedBody,
                            query_params: resolvedQueryParams,
                        };
                    }

                    const parts = ['curl'];

                    if (fakerResolved.method !== 'GET') {
                        parts.push('-X ' + fakerResolved.method);
                    }

                    let finalUrl = fakerResolved.url;
                    const qpArray = fakerResolved.query_params || [];
                    if (qpArray.length > 0) {
                        const qs = qpArray.map(qp =>
                            encodeURIComponent(qp.key) + '=' + encodeURIComponent(qp.value)
                        ).join('&');
                        finalUrl += (finalUrl.includes('?') ? '&' : '?') + qs;
                    }

                    const hasFiles = this.selectedFiles.some(f => f.file);

                    const headerObj = fakerResolved.headers || {};
                    Object.entries(headerObj).forEach(([key, val]) => {
                        if (hasFiles && key.toLowerCase() === 'content-type') return;
                        if (key) parts.push("-H '" + key.replace(/'/g, "'\\''") + ': ' + String(val).replace(/'/g, "'\\''") + "'");
                    });

                    if (hasFiles) {
                        try {
                            const parsed = JSON.parse(fakerResolved.body || '{}');
                            Object.entries(parsed).forEach(([k, v]) => {
                                if (v !== null && v !== undefined) {
                                    parts.push("--form '" + k + '=' + String(v).replace(/'/g, "'\\''") + "'");
                                }
                            });
                        } catch {}
                        this.selectedFiles.forEach(f => {
                            if (f.file) parts.push("--form '" + f.key + '=@' + f.file.name + "'");
                        });
                    } else if (['POST', 'PUT', 'PATCH'].includes(fakerResolved.method) && fakerResolved.body) {
                        parts.push("--data-raw '" + fakerResolved.body.replace(/'/g, "'\\''") + "'");
                    }

                    parts.push('--insecure');

                    const cmd = parts.join(' \\\n  ');
                    this.copyToClipboard(cmd);
                },

                // ── Colors ──

                methodColor(m) {
                    const colors = {
                        GET: 'bg-green-500', POST: 'bg-blue-500', PUT: 'bg-orange-500',
                        PATCH: 'bg-purple-500', DELETE: 'bg-red-500', HEAD: 'bg-gray-500', OPTIONS: 'bg-yellow-500',
                    };
                    return colors[m] || 'bg-gray-500';
                },

                methodBgColor(m) {
                    const colors = {
                        GET: 'text-green-700 bg-green-50 border-green-300',
                        POST: 'text-blue-700 bg-blue-50 border-blue-300',
                        PUT: 'text-orange-700 bg-orange-50 border-orange-300',
                        PATCH: 'text-purple-700 bg-purple-50 border-purple-300',
                        DELETE: 'text-red-700 bg-red-50 border-red-300',
                        HEAD: 'text-gray-700 bg-gray-50 border-gray-300',
                        OPTIONS: 'text-yellow-700 bg-yellow-50 border-yellow-300',
                    };
                    return colors[m] || 'text-gray-700 bg-gray-50 border-gray-300';
                },

                statusColor(s) {
                    if (s >= 200 && s < 300) return 'text-green-600';
                    if (s >= 300 && s < 400) return 'text-blue-600';
                    if (s >= 400 && s < 500) return 'text-orange-600';
                    if (s >= 500) return 'text-red-600';
                    return 'text-red-600';
                },

                statusBadgeColor(s) {
                    if (s >= 200 && s < 300) return 'bg-green-600 text-white';
                    if (s >= 300 && s < 400) return 'bg-blue-600 text-white';
                    if (s >= 400 && s < 500) return 'bg-orange-500 text-white';
                    if (s >= 500) return 'bg-red-600 text-white';
                    return 'bg-red-600 text-white';
                },
            };
        }
    </script>
</body>
</html>
