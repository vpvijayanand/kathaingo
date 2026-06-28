(function () {
    // Keep track of toggle state (default to enabled, saved in localStorage)
    let assistantEnabled = localStorage.getItem('kathaingo_writing_assistant_enabled') !== 'false';
    const ignoredWords = new Set();
    const stateListeners = [];
    let contextTargetNode = null; // Store reference to right-clicked node

    function updateParentButtonState(state) {
        try {
            const doc = typeof window !== 'undefined' ? (window.parent ? window.parent.document : document) : document;
            const btn = doc.getElementById('parent-writing-assistant-btn');
            if (btn) {
                if (state) {
                    btn.className = "flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full border border-burnt-orange bg-burnt-orange text-white shadow-lg shadow-burnt-orange/20 cursor-pointer transition-all duration-150";
                } else {
                    btn.className = "flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full border border-gray-800 bg-gray-950 text-gray-400 hover:text-white hover:border-gray-700 cursor-pointer transition-all duration-150";
                }
            }
        } catch (e) {
            console.error('Failed to update parent button state:', e);
        }
    }

    // Helper to notify state listeners when toggle changes
    function setAssistantEnabled(state) {
        assistantEnabled = state;
        localStorage.setItem('kathaingo_writing_assistant_enabled', state ? 'true' : 'false');
        stateListeners.forEach(listener => listener(state));
        updateParentButtonState(state);
    }

    // Sync initial state of the parent button
    setTimeout(() => {
        updateParentButtonState(assistantEnabled);
    }, 100);

    function addStateListener(listener) {
        stateListeners.push(listener);
    }

    // Register TinyMCE Writing Assistant Plugin
    tinymce.PluginManager.add('writingassistant', function (editor, url) {
        let isComposing = false;
        let scanTimeout = null;
        let activeInconsistencies = {};
        let activeOverusedWords = {};

        // Add custom styling for wavy underlines inside the editor iframe
        function onEditorInit() {
            const styleRules = `
                .kathaingo-spell-error {
                    background-color: rgba(239, 68, 68, 0.15) !important;
                    text-decoration: underline wavy #ef4444 !important;
                    text-underline-offset: 3px !important;
                    text-decoration-skip-ink: none !important;
                    cursor: pointer !important;
                }
                .kathaingo-spell-warning {
                    background-color: rgba(156, 163, 175, 0.15) !important;
                    border-bottom: 2px dashed #9ca3af !important;
                    cursor: pointer !important;
                }
                .kathaingo-punctuation-error {
                    background-color: rgba(59, 130, 246, 0.15) !important;
                    text-decoration: underline wavy #3b82f6 !important;
                    text-underline-offset: 3px !important;
                    text-decoration-skip-ink: none !important;
                    cursor: pointer !important;
                }
                .kathaingo-grammar-error {
                    background-color: rgba(168, 85, 247, 0.15) !important;
                    text-decoration: underline wavy #a855f7 !important;
                    text-underline-offset: 3px !important;
                    text-decoration-skip-ink: none !important;
                    cursor: pointer !important;
                }
                .kathaingo-style-warning {
                    background-color: rgba(16, 185, 129, 0.15) !important;
                    text-decoration: underline wavy #10b981 !important;
                    text-underline-offset: 3px !important;
                    text-decoration-skip-ink: none !important;
                    cursor: pointer !important;
                }
                .kathaingo-space-error {
                    background-color: rgba(245, 158, 11, 0.25) !important;
                    border-bottom: 2px dotted #f59e0b !important;
                    white-space: pre-wrap !important;
                    display: inline-block !important;
                    min-width: 8px !important;
                    cursor: pointer !important;
                }
                .kathaingo-consistency-warning {
                    background-color: rgba(249, 115, 22, 0.15) !important;
                    text-decoration: underline wavy #f97316 !important;
                    text-underline-offset: 3px !important;
                    text-decoration-skip-ink: none !important;
                    cursor: pointer !important;
                }
            `;
            
            if (editor.dom && typeof editor.dom.addStyle === 'function') {
                editor.dom.addStyle(styleRules);
            } else {
                const doc = editor.getDoc();
                if (doc) {
                    const style = doc.createElement('style');
                    style.type = 'text/css';
                    style.innerHTML = styleRules;
                    const target = doc.head || doc.getElementsByTagName('head')[0] || doc.documentElement;
                    if (target) target.appendChild(style);
                }
            }
            
            // Run initial scan on load (progressive paragraph checking)
            if (assistantEnabled) {
                scanAllBlocksProgressively();
            }
        }

        if (editor.initialized) {
            onEditorInit();
        } else {
            editor.on('init', onEditorInit);
        }

        // Register Editor Commands for parent page buttons to invoke
        editor.addCommand('mceWritingAssistantToggle', function () {
            const newState = !assistantEnabled;
            setAssistantEnabled(newState);
            if (newState) {
                scanAllBlocksProgressively();
            } else {
                clearAllHighlights();
            }
        });

        editor.addCommand('mceReviewArticle', function () {
            reviewArticleFlow();
        });

        // 1. Toggle Button
        editor.ui.registry.addToggleButton('writingassistant', {
            icon: 'spell-check',
            tooltip: 'எழுத்துதவியாளர்',
            onAction: function () {
                const newState = !assistantEnabled;
                setAssistantEnabled(newState);
                if (newState) {
                    scanAllBlocksProgressively();
                } else {
                    clearAllHighlights();
                }
            },
            onSetup: function (buttonApi) {
                const stateCallback = function (state) {
                    buttonApi.setActive(state);
                };
                addStateListener(stateCallback);
                buttonApi.setActive(assistantEnabled);
                return function () {
                    const idx = stateListeners.indexOf(stateCallback);
                    if (idx > -1) stateListeners.splice(idx, 1);
                };
            }
        });

        function reviewArticleFlow() {
            const body = editor.getBody();
            const text = body.textContent || '';
            
            const reviewUrl = (window.Kathaingo && window.Kathaingo.routes && window.Kathaingo.routes.reviewArticle) || '/api/writing-assistant/review-article';
            
            editor.setProgressState(true);
            
            fetch(reviewUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ text: text })
            })
            .then(res => {
                editor.setProgressState(false);
                if (!res.ok) {
                    console.error('Review Article API error: HTTP status ' + res.status);
                    return res.text().then(t => { throw new Error(t); });
                }
                return res.json();
            })
            .then(data => {
                if (data.summary) {
                    editor.windowManager.open({
                        title: 'Article Review Summary (மதிப்பாய்வு அறிக்கை)',
                        body: {
                            type: 'panel',
                            items: [
                                {
                                    type: 'htmlpanel',
                                    html: `
                                        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 12px; line-height: 1.5; color: #1e293b;">
                                            <h3 style="margin-top: 0; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; font-size: 16px;">Review Results</h3>
                                            <table style="width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 14px;">
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding: 10px 0; color: #475569;">Spelling Errors (எழுத்துப்பிழை)</td>
                                                    <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #ef4444; font-size: 16px;">${data.summary.spelling}</td>
                                                </tr>
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding: 10px 0; color: #475569;">Grammar Issues (இலக்கணம்)</td>
                                                    <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #a855f7; font-size: 16px;">${data.summary.grammar}</td>
                                                </tr>
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding: 10px 0; color: #475569;">Style Warnings (நடை)</td>
                                                    <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #10b981; font-size: 16px;">${data.summary.style}</td>
                                                </tr>
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding: 10px 0; color: #475569;">Inconsistencies (ஒருமைப்பாடு)</td>
                                                    <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #f97316; font-size: 16px;">${data.summary.consistency}</td>
                                                </tr>
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding: 10px 0; color: #475569;">Unknown Words (புதிய சொற்கள்)</td>
                                                    <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #f59e0b; font-size: 16px;">${data.summary.unknown}</td>
                                                </tr>
                                                <tr style="background-color: #f8fafc; font-weight: bold; font-size: 14px;">
                                                    <td style="padding: 12px 10px; color: #0f172a;">Readability (வாசிப்புத் தன்மை)</td>
                                                    <td style="padding: 12px 10px; text-align: right; color: #2563eb; font-size: 15px;">${data.summary.readability}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    `
                                }
                            ]
                        },
                        buttons: [
                            {
                                type: 'submit',
                                text: 'Highlight Errors in Editor',
                                primary: true
                            },
                            {
                                type: 'cancel',
                                text: 'Close'
                            }
                        ],
                        onSubmit: function (api) {
                            if (!assistantEnabled) {
                                setAssistantEnabled(true);
                            }
                            scanAllBlocksProgressively();
                            api.close();
                        }
                    });
                }
            })
            .catch(err => {
                editor.setProgressState(false);
                console.error('Error reviewing article:', err);
                editor.notificationManager.open({
                    text: 'An error occurred while reviewing the article.',
                    type: 'error',
                    timeout: 4000
                });
            });
        }

        editor.ui.registry.addButton('reviewarticle', {
            text: '✓ Review Article',
            tooltip: 'பதிவை மதிப்பிடு',
            onAction: function () {
                reviewArticleFlow();
            }
        });

        // 2. IME Composition Protection
        editor.on('compositionstart', function () {
            isComposing = true;
            if (scanTimeout) clearTimeout(scanTimeout);
        });

        // 3. Debounced scan trigger helper
        function debounceScan(scanAll) {
            if (scanTimeout) clearTimeout(scanTimeout);
            scanTimeout = setTimeout(function () {
                if (scanAll) {
                    scanAllBlocksProgressively();
                } else {
                    const activeNode = editor.selection.getNode();
                    triggerBlockScan(activeNode);
                }
            }, 800);
        }

        editor.on('compositionend', function () {
            isComposing = false;
            debounceScan(false);
        });

        // 4. Editor Content Listeners for Real-time Progressive Scanning
        editor.on('keyup', function () {
            if (!assistantEnabled || isComposing) return;
            debounceScan(false);
        });

        editor.on('change paste undo redo SetContent', function () {
            if (!assistantEnabled) return;
            debounceScan(true);
        });

        // Trigger block scan when cursor moves to a different element
        editor.on('NodeChange', function (e) {
            if (!assistantEnabled) return;
            triggerBlockScan(e.element);
        });

        // 5. Dynamic Context Menu API for Suggestions
        editor.ui.registry.addNestedMenuItem('spelling-suggestions', {
            text: 'Suggestions (பரிந்துரைகள்)',
            getSubmenuItems: function () {
                const targetNode = contextTargetNode;
                if (targetNode && (targetNode.classList.contains('kathaingo-spell-error') || targetNode.classList.contains('kathaingo-spell-warning'))) {
                    const suggestionsAttr = targetNode.getAttribute('data-suggestions');
                    const suggestions = suggestionsAttr ? suggestionsAttr.split('|') : [];
                    if (suggestions.length === 0) {
                        return [{
                            type: 'menuitem',
                            text: '(No suggestions / பரிந்துரைகள் இல்லை)',
                            enabled: false
                        }];
                    }
                    const items = [];
                    suggestions.forEach(function (sugg) {
                        items.push({
                            type: 'menuitem',
                            text: 'Fix this occurrence: "' + sugg + '"',
                            onAction: function () {
                                replaceNodeContent(targetNode, sugg);
                            }
                        });
                        
                        const word = targetNode.getAttribute('data-word');
                        const type = targetNode.getAttribute('data-type');
                        const count = editor.getBody().querySelectorAll('span[data-word="' + word + '"]').length;
                        if (count > 1) {
                            items.push({
                                type: 'menuitem',
                                text: 'Fix all occurrences in this article: "' + sugg + '"',
                                onAction: function () {
                                    replaceAllOccurrences(word, sugg, type);
                                }
                            });
                        }
                    });
                    return items;
                }
                return [];
            }
        });

        editor.ui.registry.addNestedMenuItem('consistency-suggestions', {
            text: 'Consistency (ஒருமைப்பாடு)',
            getSubmenuItems: function () {
                const targetNode = contextTargetNode;
                if (targetNode && targetNode.classList.contains('kathaingo-consistency-warning')) {
                    const suggestionsAttr = targetNode.getAttribute('data-suggestions');
                    const suggestions = suggestionsAttr ? suggestionsAttr.split('|') : [];
                    const items = [];
                    
                    if (targetNode.getAttribute('data-message')) {
                        items.push({
                            type: 'menuitem',
                            text: targetNode.getAttribute('data-message'),
                            enabled: false
                        });
                    }

                    if (suggestions.length > 0) {
                        suggestions.forEach(function (sugg) {
                            items.push({
                                type: 'menuitem',
                                text: 'Fix this occurrence: "' + sugg + '"',
                                onAction: function () {
                                    replaceNodeContent(targetNode, sugg);
                                }
                            });

                            const word = targetNode.getAttribute('data-word');
                            const type = targetNode.getAttribute('data-type');
                            const count = editor.getBody().querySelectorAll('span[data-word="' + word + '"]').length;
                            if (count > 1) {
                                items.push({
                                    type: 'menuitem',
                                    text: 'Fix all occurrences in this article: "' + sugg + '"',
                                    onAction: function () {
                                        replaceAllOccurrences(word, sugg, type);
                                    }
                                });
                            }
                        });
                    }
                    return items;
                }
                return [];
            }
        });

        editor.ui.registry.addNestedMenuItem('punctuation-suggestions', {
            text: 'Fix Punctuation (நிறுத்தற்குறி திருத்தம்)',
            getSubmenuItems: function () {
                const targetNode = contextTargetNode;
                if (targetNode && targetNode.classList.contains('kathaingo-punctuation-error')) {
                    const suggestionsAttr = targetNode.getAttribute('data-suggestions');
                    const suggestions = suggestionsAttr ? suggestionsAttr.split('|') : [];
                    const items = [];
                    
                    if (targetNode.getAttribute('data-message')) {
                        items.push({
                            type: 'menuitem',
                            text: targetNode.getAttribute('data-message'),
                            enabled: false
                        });
                    }

                    if (suggestions.length > 0) {
                        suggestions.forEach(function (sugg) {
                            items.push({
                                type: 'menuitem',
                                text: 'Fix this occurrence: "' + sugg + '"',
                                onAction: function () {
                                    replaceNodeContent(targetNode, sugg);
                                }
                            });

                            const word = targetNode.getAttribute('data-word');
                            const type = targetNode.getAttribute('data-type');
                            const count = editor.getBody().querySelectorAll('span[data-word="' + word + '"]').length;
                            if (count > 1) {
                                items.push({
                                    type: 'menuitem',
                                    text: 'Fix all occurrences in this article: "' + sugg + '"',
                                    onAction: function () {
                                        replaceAllOccurrences(word, sugg, type);
                                    }
                                });
                            }
                        });
                    } else {
                        items.push({
                            type: 'menuitem',
                            text: '(Check context details)',
                            enabled: false
                        });
                    }
                    return items;
                }
                return [];
            }
        });

        editor.ui.registry.addNestedMenuItem('grammar-suggestions', {
            text: 'Grammar / Sandhi (இலக்கணம் / சந்தி)',
            getSubmenuItems: function () {
                const targetNode = contextTargetNode;
                if (targetNode && targetNode.classList.contains('kathaingo-grammar-error')) {
                    const suggestionsAttr = targetNode.getAttribute('data-suggestions');
                    const suggestions = suggestionsAttr ? suggestionsAttr.split('|') : [];
                    const items = [];

                    if (targetNode.getAttribute('data-message')) {
                        items.push({
                            type: 'menuitem',
                            text: targetNode.getAttribute('data-message'),
                            enabled: false
                        });
                    }

                    if (suggestions.length > 0) {
                        suggestions.forEach(function (sugg) {
                            items.push({
                                type: 'menuitem',
                                text: 'Fix this occurrence: "' + sugg + '"',
                                onAction: function () {
                                    replaceNodeContent(targetNode, sugg);
                                }
                            });

                            const word = targetNode.getAttribute('data-word');
                            const type = targetNode.getAttribute('data-type');
                            const count = editor.getBody().querySelectorAll('span[data-word="' + word + '"]').length;
                            if (count > 1) {
                                items.push({
                                    type: 'menuitem',
                                    text: 'Fix all occurrences in this article: "' + sugg + '"',
                                    onAction: function () {
                                        replaceAllOccurrences(word, sugg, type);
                                    }
                                });
                            }
                        });
                    }
                    return items;
                }
                return [];
            }
        });

        editor.ui.registry.addNestedMenuItem('style-suggestions', {
            text: 'Writing Style / Spaces (எழுத்து நடை / இடைவெளி)',
            getSubmenuItems: function () {
                const targetNode = contextTargetNode;
                if (targetNode && (targetNode.classList.contains('kathaingo-style-warning') || targetNode.classList.contains('kathaingo-space-error'))) {
                    const suggestionsAttr = targetNode.getAttribute('data-suggestions');
                    const suggestions = suggestionsAttr ? suggestionsAttr.split('|') : [];
                    const items = [];

                    if (targetNode.getAttribute('data-message')) {
                        items.push({
                            type: 'menuitem',
                            text: targetNode.getAttribute('data-message'),
                            enabled: false
                        });
                    }

                    if (suggestions.length > 0) {
                        suggestions.forEach(function (sugg) {
                            items.push({
                                type: 'menuitem',
                                text: 'Fix this occurrence: "' + sugg + '"',
                                onAction: function () {
                                    replaceNodeContent(targetNode, sugg);
                                }
                            });

                            const word = targetNode.getAttribute('data-word');
                            const type = targetNode.getAttribute('data-type');
                            const count = editor.getBody().querySelectorAll('span[data-word="' + word + '"]').length;
                            if (count > 1) {
                                items.push({
                                    type: 'menuitem',
                                    text: 'Fix all occurrences in this article: "' + sugg + '"',
                                    onAction: function () {
                                        replaceAllOccurrences(word, sugg, type);
                                    }
                                });
                            }
                        });
                    }
                    return items;
                }
                return [];
            }
        });

        editor.ui.registry.addMenuItem('writing-ignore-once', {
            text: 'Ignore Once (தற்காலிகமாக தவிர்)',
            onAction: function () {
                const node = contextTargetNode;
                if (node) {
                    const word = node.getAttribute('data-word') || node.textContent;
                    ignoredWords.add(word);
                    unwrapNode(node);
                }
            }
        });

        editor.ui.registry.addMenuItem('writing-add-dictionary', {
            text: 'Add to Personal Dictionary (அகராதியில் சேர்)',
            onAction: function () {
                const node = contextTargetNode;
                if (node) {
                    const word = node.getAttribute('data-word') || node.textContent;
                    const lang = detectLanguage(word);

                    const addUrl = (window.Kathaingo && window.Kathaingo.routes && window.Kathaingo.routes.addDictionary) || '/api/writing-assistant/dictionary/add';
                    fetch(addUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ word: word, language: lang })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            unwrapNode(node);
                        }
                    })
                    .catch(err => console.error('Error adding to dictionary:', err));
                }
            }
        });

        editor.ui.registry.addMenuItem('writing-suggest-community', {
            text: 'Suggest to Community (பொது அகராதிக்கு பரிந்துரை)',
            onAction: function () {
                const node = contextTargetNode;
                if (node) {
                    const word = node.getAttribute('data-word') || node.textContent;
                    const lang = detectLanguage(word);

                    const suggestUrl = (window.Kathaingo && window.Kathaingo.routes && window.Kathaingo.routes.suggestWord) || '/api/writing-assistant/suggest-word';
                    fetch(suggestUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ word: word, language: lang })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            editor.notificationManager.open({
                                text: 'Word suggested for community review (பரிந்துரை சமர்ப்பிக்கப்பட்டது).',
                                type: 'success',
                                timeout: 3000
                            });
                        }
                    })
                    .catch(err => console.error('Error nominating word:', err));
                }
            }
        });

        editor.ui.registry.addContextMenu('writingassistant', {
            update: function (element) {
                const targetNode = getWritingAssistantNode(element);
                contextTargetNode = targetNode; // Keep track of the node that was right-clicked
                
                if (!targetNode) return '';

                if (targetNode.classList.contains('kathaingo-spell-error')) {
                    return 'spelling-suggestions writing-ignore-once writing-add-dictionary writing-suggest-community';
                }
                if (targetNode && (targetNode.classList.contains('kathaingo-spell-error') || targetNode.classList.contains('kathaingo-spell-warning'))) {
                    return 'spelling-suggestions';
                }
                if (targetNode && targetNode.classList.contains('kathaingo-punctuation-error')) {
                    return 'punctuation-suggestions writing-ignore-once';
                }
                if (targetNode && targetNode.classList.contains('kathaingo-grammar-error')) {
                    return 'grammar-suggestions writing-ignore-once';
                }
                if (targetNode && (targetNode.classList.contains('kathaingo-style-warning') || targetNode.classList.contains('kathaingo-space-error'))) {
                    return 'style-suggestions writing-ignore-once';
                }
                if (targetNode && targetNode.classList.contains('kathaingo-consistency-warning')) {
                    return 'consistency-suggestions writing-ignore-once';
                }
                return '';
            }
        });

        // 6. Non-Destructive HTML Save Clean-up
        editor.on('BeforeGetContent', function (e) {
            if (e.format === 'raw') return;
            if (e.content) {
                const doc = new DOMParser().parseFromString(e.content, 'text/html');
                let modified = false;
                doc.querySelectorAll('.kathaingo-spell-error, .kathaingo-spell-warning, .kathaingo-punctuation-error, .kathaingo-grammar-error, .kathaingo-style-warning, .kathaingo-space-error, .kathaingo-consistency-warning').forEach(function (el) {
                    el.replaceWith(el.textContent);
                    modified = true;
                });
                if (modified) {
                    e.content = doc.body.innerHTML;
                }
            }
        });

        // Helpers

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   window.parent?.document?.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                   window.Laravel?.csrfToken ||
                   window.parent?.Laravel?.csrfToken ||
                   '';
        }

        function detectLanguage(word) {
            return /[\u0B80-\u0BFF]/.test(word) ? 'ta' : 'en';
        }

        function getWritingAssistantNode(node) {
            if (!node || node === editor.getBody()) return null;
            if (node.classList && (
                node.classList.contains('kathaingo-spell-error') || 
                node.classList.contains('kathaingo-spell-warning') ||
                node.classList.contains('kathaingo-punctuation-error') || 
                node.classList.contains('kathaingo-grammar-error') || 
                node.classList.contains('kathaingo-style-warning') ||
                node.classList.contains('kathaingo-space-error') ||
                node.classList.contains('kathaingo-consistency-warning')
            )) {
                return node;
            }
            return getWritingAssistantNode(node.parentNode);
        }

        function replaceNodeContent(node, replacement) {
            if (!node) return;
            
            // If a background scan completed while the context menu was open, 
            // the original node might have been replaced and detached from the DOM.
            // We need to find the live node in the editor.
            const doc = editor.getDoc();
            if (!doc.contains(node)) {
                let liveNodeFound = false;
                // Check if the current cursor is inside the new span
                const currentCursorNode = getWritingAssistantNode(editor.selection.getNode());
                if (currentCursorNode && currentCursorNode.getAttribute('data-word') === node.getAttribute('data-word')) {
                    node = currentCursorNode;
                    liveNodeFound = true;
                } else {
                    // Fallback: search the active block for the same word
                    const block = getClosestBlock(editor.selection.getNode());
                    if (block) {
                        const word = node.getAttribute('data-word');
                        const spans = block.querySelectorAll('.kathaingo-spell-error, .kathaingo-spell-warning, .kathaingo-punctuation-error, .kathaingo-grammar-error, .kathaingo-style-warning, .kathaingo-space-error, .kathaingo-consistency-warning');
                        for (let i = 0; i < spans.length; i++) {
                            if (spans[i].getAttribute('data-word') === word) {
                                node = spans[i];
                                liveNodeFound = true;
                                break;
                            }
                        }
                    }
                }
                
                if (!liveNodeFound) {
                    console.error("Writing assistant: Could not find the live node to replace. It may have been modified.");
                    return;
                }
            }

            const original = node.getAttribute('data-word') || node.textContent.trim();
            const corrected = replacement.trim();
            const lang = detectLanguage(original);
            const type = node.getAttribute('data-type');

            // Use direct DOM manipulation inside an undo transaction.
            try {
                const performReplacement = function() {
                    node.textContent = replacement;
                    unwrapNode(node);
                };
                
                if (editor.undoManager && typeof editor.undoManager.transact === 'function') {
                    editor.undoManager.transact(performReplacement);
                } else {
                    performReplacement();
                }

                reportCorrection(original, corrected, lang, 1);

                // Proactively prompt to replace all similar occurrences (Word-like behavior)
                const otherSpans = editor.getBody().querySelectorAll('span[data-word="' + original + '"]');
                const otherCount = Array.from(otherSpans).filter(function(n) {
                    return n.getAttribute('data-type') === type;
                }).length;

                if (otherCount > 0) {
                    const totalCount = otherCount + 1;
                    editor.windowManager.confirm(
                        'Replace all ' + totalCount + ' occurrences of "' + original + '" with "' + corrected + '"?',
                        function (confirmed) {
                            if (confirmed) {
                                replaceAllOccurrences(original, corrected, type);
                            }
                        }
                    );
                }
            } catch (err) {
                console.error('Writing assistant replacement failed:', err);
                try {
                    node.textContent = replacement;
                    unwrapNode(node);
                } catch (fallbackErr) {}
            }

            // Trigger TinyMCE update notifications
            try {
                if (typeof editor.nodeChanged === 'function') {
                    editor.nodeChanged();
                }
                if (typeof editor.fire === 'function') {
                    editor.fire('change');
                    editor.fire('input');
                }
            } catch (evtErr) {}
        }

        function replaceAllOccurrences(word, replacement, type) {
            if (!word) return;
            
            let count = 0;
            try {
                const performBulkReplacement = function() {
                    const spans = editor.getBody().querySelectorAll('span[data-word="' + word + '"]');
                    spans.forEach(function(node) {
                        if (!type || node.getAttribute('data-type') === type) {
                            node.textContent = replacement;
                            unwrapNode(node);
                            count++;
                        }
                    });
                };
                
                if (editor.undoManager && typeof editor.undoManager.transact === 'function') {
                    editor.undoManager.transact(performBulkReplacement);
                } else {
                    performBulkReplacement();
                }

                if (count > 0) {
                    const lang = detectLanguage(word);
                    reportCorrection(word, replacement, lang, count);
                }
            } catch (err) {
                console.error('Writing assistant bulk replacement failed:', err);
            }

            try {
                if (typeof editor.nodeChanged === 'function') {
                    editor.nodeChanged();
                }
                if (typeof editor.fire === 'function') {
                    editor.fire('change');
                    editor.fire('input');
                }
            } catch (evtErr) {}
        }

        function reportCorrection(original, corrected, lang, count) {
            if (!original || !corrected || original === corrected) return;
            
            const learnUrl = (window.Kathaingo && window.Kathaingo.routes && window.Kathaingo.routes.learnCorrection) || '/api/writing-assistant/learn-correction';
            
            fetch(learnUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    original_text: original,
                    corrected_text: corrected,
                    language: lang,
                    count: count || 1
                })
            })
            .then(res => {
                if (!res.ok) {
                    // silent fail or log
                }
            })
            .catch(err => console.error('Error reporting writing assistant learning correction:', err));
        }

        function unwrapNode(node) {
            if (!node) return;
            if (editor.dom && typeof editor.dom.remove === 'function') {
                editor.dom.remove(node, true);
            } else {
                const parent = node.parentNode;
                if (!parent) return;
                while (node.firstChild) {
                    parent.insertBefore(node.firstChild, node);
                }
                parent.removeChild(node);
            }
        }

        function getClosestBlock(node) {
            if (!node) return null;
            if (node === editor.getBody()) return node;
            const blockTags = ['P', 'LI', 'BLOCKQUOTE', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'DIV'];
            const tagName = node.tagName ? node.tagName.toUpperCase() : '';
            if (blockTags.indexOf(tagName) > -1) {
                return node;
            }
            return getClosestBlock(node.parentNode);
        }

        // Trigger block-level scan
        function triggerBlockScan(node) {
            if (!assistantEnabled) return;
            const block = getClosestBlock(node);
            if (!block) return;

            // Detect manual inline corrections
            const existingSpans = block.querySelectorAll('.kathaingo-spell-error, .kathaingo-spell-warning, .kathaingo-punctuation-error, .kathaingo-grammar-error, .kathaingo-style-warning');
            existingSpans.forEach(function (span) {
                const original = span.getAttribute('data-word');
                const currentText = span.textContent.trim();
                if (original && currentText && original !== currentText && currentText.length > 0) {
                    const lang = detectLanguage(original);
                    reportCorrection(original, currentText, lang, 1);
                }
            });

            const clone = block.cloneNode(true);
            clone.querySelectorAll('.kathaingo-spell-error, .kathaingo-spell-warning, .kathaingo-punctuation-error, .kathaingo-grammar-error, .kathaingo-style-warning').forEach(function (el) {
                el.replaceWith(el.textContent);
            });
            const text = clone.textContent.trim();

            if (text.length === 0) {
                removeBlockHighlights(block);
                return;
            }

            const checkUrl = (window.Kathaingo && window.Kathaingo.routes && window.Kathaingo.routes.checkBlock) || '/api/writing-assistant/check-block';
            fetch(checkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ text: text })
            })
            .then(res => {
                if (!res.ok) {
                    console.error('Writing Assistant API error: HTTP status ' + res.status);
                    return res.text().then(t => { throw new Error(t); });
                }
                return res.json();
            })
            .then(data => {
                if (data.matches) {
                    let activeMatches = data.matches.filter(m => !ignoredWords.has(m.text));
                    
                    // Generate consistency matches
                    const consistencyMatches = [];
                    for (const word in activeInconsistencies) {
                        if (activeInconsistencies.hasOwnProperty(word) && !ignoredWords.has(word)) {
                            let idx = text.indexOf(word);
                            while (idx !== -1) {
                                // Check word boundary
                                let isWord = true;
                                if (idx > 0) {
                                    const charBefore = text.charAt(idx - 1);
                                    if (/[\u0B80-\u0BFFa-zA-Z0-9]/.test(charBefore)) {
                                        isWord = false;
                                    }
                                }
                                if (idx + word.length < text.length) {
                                    const charAfter = text.charAt(idx + word.length);
                                    if (/[\u0B80-\u0BFFa-zA-Z0-9]/.test(charAfter)) {
                                        isWord = false;
                                    }
                                }
                                
                                if (isWord) {
                                    consistencyMatches.push({
                                        offset: idx,
                                        length: word.length,
                                        text: word,
                                        type: 'consistency',
                                        suggestions: [activeInconsistencies[word].preferred],
                                        message: activeInconsistencies[word].message
                                    });
                                }
                                idx = text.indexOf(word, idx + 1);
                            }
                        }
                    }
                    
                    // Filter consistency matches that do not overlap with existing api matches
                    const nonOverlappingConsistency = consistencyMatches.filter(cm => {
                        const cmStart = cm.offset;
                        const cmEnd = cm.offset + cm.length;
                        return !activeMatches.some(am => {
                            const amStart = am.offset;
                            const amEnd = am.offset + am.length;
                            return cmStart < amEnd && amStart < cmEnd;
                        });
                    });
                    
                    activeMatches = activeMatches.concat(nonOverlappingConsistency);

                    // Generate overuse matches
                    const overuseMatches = [];
                    for (const word in activeOverusedWords) {
                        if (activeOverusedWords.hasOwnProperty(word) && !ignoredWords.has(word)) {
                            let idx = text.indexOf(word);
                            while (idx !== -1) {
                                // Check word boundary
                                let isWord = true;
                                if (idx > 0) {
                                    const charBefore = text.charAt(idx - 1);
                                    if (/[\u0B80-\u0BFFa-zA-Z0-9]/.test(charBefore)) {
                                        isWord = false;
                                    }
                                }
                                if (idx + word.length < text.length) {
                                    const charAfter = text.charAt(idx + word.length);
                                    if (/[\u0B80-\u0BFFa-zA-Z0-9]/.test(charAfter)) {
                                        isWord = false;
                                    }
                                }
                                
                                if (isWord) {
                                    overuseMatches.push({
                                        offset: idx,
                                        length: word.length,
                                        text: word,
                                        type: 'style',
                                        suggestions: [],
                                        message: activeOverusedWords[word].message
                                    });
                                }
                                idx = text.indexOf(word, idx + 1);
                            }
                        }
                    }
                    
                    // Filter overuse matches that do not overlap with existing matches
                    const nonOverlappingOveruse = overuseMatches.filter(om => {
                        const omStart = om.offset;
                        const omEnd = om.offset + om.length;
                        return !activeMatches.some(am => {
                            const amStart = am.offset;
                            const amEnd = am.offset + am.length;
                            return omStart < amEnd && amStart < omEnd;
                        });
                    });
                    
                    activeMatches = activeMatches.concat(nonOverlappingOveruse);
                    
                    applyBlockHighlights(block, activeMatches);
                }
            })
            .catch(err => console.error('Writing assistant block check error:', err));
        }

        function removeBlockHighlights(block) {
            if (!block) return;
            const spans = block.querySelectorAll('.kathaingo-spell-error, .kathaingo-spell-warning, .kathaingo-punctuation-error, .kathaingo-grammar-error, .kathaingo-style-warning, .kathaingo-space-error, .kathaingo-consistency-warning');
            spans.forEach(span => {
                unwrapNode(span);
            });
            block.normalize();
        }

        // Helper to count character lengths of elements recursively
        function getNodeCharacterLength(node) {
            if (node.nodeType === 3) {
                return node.textContent.length;
            }
            let len = 0;
            for (let i = 0; i < node.childNodes.length; i++) {
                len += getNodeCharacterLength(node.childNodes[i]);
            }
            return len;
        }

        // Get cursor character offset inside block
        function getCursorCharacterOffset(block) {
            const range = editor.selection.getRng();
            if (!range) return -1;
            
            let offset = 0;
            let found = false;
            
            function traverse(node) {
                if (found) return;
                
                if (node === range.startContainer) {
                    if (node.nodeType === 3) {
                        offset += range.startOffset;
                    } else {
                        for (let i = 0; i < range.startOffset && i < node.childNodes.length; i++) {
                            offset += getNodeCharacterLength(node.childNodes[i]);
                        }
                    }
                    found = true;
                    return;
                }
                
                if (node.nodeType === 3) {
                    offset += node.textContent.length;
                } else {
                    for (let i = 0; i < node.childNodes.length; i++) {
                        traverse(node.childNodes[i]);
                    }
                }
            }
            
            traverse(block);
            return found ? offset : -1;
        }

        // Restore cursor character offset inside block
        function setCursorCharacterOffset(block, targetOffset) {
            if (targetOffset < 0) return;
            
            let currentOffset = 0;
            let set = false;
            
            function traverse(node) {
                if (set) return;
                if (node.nodeType === 3) {
                    const len = node.textContent.length;
                    if (currentOffset + len >= targetOffset) {
                        const localOffset = targetOffset - currentOffset;
                        const range = editor.getDoc().createRange();
                        range.setStart(node, localOffset);
                        range.setEnd(node, localOffset);
                        editor.selection.setRng(range);
                        set = true;
                        return;
                    }
                    currentOffset += len;
                } else {
                    for (let i = 0; i < node.childNodes.length; i++) {
                        traverse(node.childNodes[i]);
                    }
                }
            }
            
            traverse(block);
            
            // Fallback to the end if offset is larger than text nodes length
            if (!set) {
                const textNodes = [];
                function gatherTextNodes(n) {
                    if (n.nodeType === 3) textNodes.push(n);
                    else {
                        for (let i = 0; i < n.childNodes.length; i++) {
                            gatherTextNodes(n.childNodes[i]);
                        }
                    }
                }
                gatherTextNodes(block);
                
                if (textNodes.length > 0) {
                    const lastNode = textNodes[textNodes.length - 1];
                    const range = editor.getDoc().createRange();
                    range.setStart(lastNode, lastNode.textContent.length);
                    range.setEnd(lastNode, lastNode.textContent.length);
                    editor.selection.setRng(range);
                } else {
                    const range = editor.getDoc().createRange();
                    range.setStart(block, 0);
                    range.setEnd(block, 0);
                    editor.selection.setRng(range);
                }
            }
        }

        // Apply highlights recursively using DOM splits
        function applyBlockHighlights(block, matches) {
            const hasFocus = editor.hasFocus();
            // Get cursor offset relative to block BEFORE DOM manipulation
            const originalCursorOffset = hasFocus ? getCursorCharacterOffset(block) : -1;

            removeBlockHighlights(block);

            if (matches.length === 0) {
                if (hasFocus && originalCursorOffset >= 0) {
                    setCursorCharacterOffset(block, originalCursorOffset);
                }
                return;
            }

            // Sort matches descending by offset to avoid split index shifts
            matches.sort((a, b) => b.offset - a.offset);

            // Traverse text nodes recursively
            const textNodes = [];
            let currentOffset = 0;

            function traverse(n) {
                if (n.nodeType === 3) {
                    const len = n.textContent.length;
                    textNodes.push({
                        node: n,
                        start: currentOffset,
                        end: currentOffset + len
                    });
                    currentOffset += len;
                } else {
                    for (let i = 0; i < n.childNodes.length; i++) {
                        traverse(n.childNodes[i]);
                    }
                }
            }
            traverse(block);

            matches.forEach(function (match) {
                // Find node containing match
                let targetTextNodeInfo = null;
                for (let i = 0; i < textNodes.length; i++) {
                    const info = textNodes[i];
                    if (match.offset >= info.start && (match.offset + match.length) <= info.end) {
                        targetTextNodeInfo = info;
                        break;
                    }
                }

                if (!targetTextNodeInfo) return; // Skip if spans across boundary tags

                const node = targetTextNodeInfo.node;
                const localStart = match.offset - targetTextNodeInfo.start;

                // Split text node
                const postNode = node.splitText(localStart + match.length);
                const isolatedNode = node.splitText(localStart);

                // Create wrapper span
                const doc = editor.getDoc();
                if (!doc) return;
                const span = doc.createElement('span');
                if (match.type === 'spelling') {
                    span.className = 'kathaingo-spell-error';
                } else if (match.type === 'spelling-warning') {
                    span.className = 'kathaingo-spell-warning';
                } else if (match.type === 'punctuation') {
                    span.className = 'kathaingo-punctuation-error';
                } else if (match.type === 'grammar') {
                    span.className = 'kathaingo-grammar-error';
                } else if (match.type === 'style') {
                    if (/^\s+$/.test(match.text)) {
                        span.className = 'kathaingo-space-error';
                    } else {
                        span.className = 'kathaingo-style-warning';
                    }
                } else if (match.type === 'consistency') {
                    span.className = 'kathaingo-consistency-warning';
                }

                span.setAttribute('data-type', match.type);
                span.setAttribute('data-word', match.text);
                if (match.suggestions && match.suggestions.length > 0) {
                    span.setAttribute('data-suggestions', match.suggestions.join('|'));
                }
                if (match.message) {
                    span.setAttribute('data-message', match.message);
                }

                if (isolatedNode.parentNode) {
                    isolatedNode.parentNode.replaceChild(span, isolatedNode);
                    span.appendChild(isolatedNode);
                }

                // Update text nodes collection to reflect the split
                const targetIdx = textNodes.indexOf(targetTextNodeInfo);
                if (targetIdx > -1) {
                    textNodes.splice(targetIdx, 1, 
                        { node: node, start: targetTextNodeInfo.start, end: targetTextNodeInfo.start + localStart },
                        { node: postNode, start: targetTextNodeInfo.start + localStart + match.length, end: targetTextNodeInfo.end }
                    );
                }
            });

            // Restore selection offset relative to new DOM structure
            if (hasFocus && originalCursorOffset >= 0) {
                setCursorCharacterOffset(block, originalCursorOffset);
            }
        }

        // Process all paragraphs in progressive async batches
        function scanAllBlocksProgressively() {
            const body = editor.getBody();
            const text = body.textContent || '';
            
            const consistencyUrl = (window.Kathaingo && window.Kathaingo.routes && window.Kathaingo.routes.analyzeConsistency) || '/api/writing-assistant/analyze-consistency';
            
            fetch(consistencyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ text: text })
            })
            .then(res => {
                if (!res.ok) {
                    console.error('Consistency API error: HTTP status ' + res.status);
                    return res.json().then(t => { throw new Error(t); });
                }
                return res.json();
            })
            .then(data => {
                if (data.inconsistencies) {
                    activeInconsistencies = data.inconsistencies;
                } else {
                    activeInconsistencies = {};
                }
                if (data.overused) {
                    activeOverusedWords = data.overused;
                } else {
                    activeOverusedWords = {};
                }
            })
            .catch(err => {
                console.error('Error fetching consistency details:', err);
                activeInconsistencies = {};
                activeOverusedWords = {};
            })
            .finally(() => {
                const blocks = body.querySelectorAll('p, li, blockquote, h1, h2, h3, h4, h5, h6');
                let i = 0;
                function nextBatch() {
                    const limit = Math.min(i + 5, blocks.length);
                    for (; i < limit; i++) {
                        triggerBlockScan(blocks[i]);
                    }
                    if (i < blocks.length) {
                        setTimeout(nextBatch, 200);
                    }
                }
                
                if (blocks.length > 0) {
                    nextBatch();
                } else {
                    triggerBlockScan(body);
                }
            });
        }

        function clearAllHighlights() {
            const body = editor.getBody();
            body.querySelectorAll('.kathaingo-spell-error, .kathaingo-spell-warning, .kathaingo-punctuation-error, .kathaingo-grammar-error, .kathaingo-style-warning, .kathaingo-space-error, .kathaingo-consistency-warning').forEach(function (el) {
                unwrapNode(el);
            });
            body.normalize();
        }
    });
})();
