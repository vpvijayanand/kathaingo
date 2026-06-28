(function () {
    window.KathaingoLanguageHelper = {
        init: function (options) {
            const isLanguageHelperEnabled = options.enabled;
            if (!isLanguageHelperEnabled) return;

            const suggestUrl = options.suggestUrl;
            const csrfToken = options.csrfToken;
            const inputSelector = options.inputSelector || '.comment-input-field';

            let currentInputMode = 'en';

            // Safe localStorage wrappers to prevent SecurityError exceptions in Private/Incognito modes
            function safeGetItem(key, defaultValue) {
                try {
                    return localStorage.getItem(key) || defaultValue;
                } catch (e) {
                    return defaultValue;
                }
            }

            function safeSetItem(key, value) {
                try {
                    return localStorage.setItem(key, value);
                } catch (e) {}
            }

            // --- Input Mode Toggle Sync & LocalStorage Setup ---
            function syncLangToggles(mode) {
                currentInputMode = mode;
                safeSetItem('kathaingo_input_mode', mode);
                document.querySelectorAll('.lang-toggle-btn').forEach(btn => {
                    const btnLang = btn.getAttribute('data-lang');
                    if (btnLang === mode) {
                        btn.classList.remove('bg-transparent', 'text-gray-400', 'hover:text-white');
                        btn.classList.add('bg-burnt-orange', 'text-white');
                    } else {
                        btn.classList.remove('bg-burnt-orange', 'text-white');
                        btn.classList.add('bg-transparent', 'text-gray-400', 'hover:text-white');
                    }
                });
            }

            // Default to 'en' as requested by the user
            currentInputMode = safeGetItem('kathaingo_input_mode', 'en');
            setTimeout(() => syncLangToggles(currentInputMode), 0);

            // Delegate click event for dynamic or static toggle buttons
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.lang-toggle-btn');
                if (!btn) return;
                e.preventDefault();
                const mode = btn.getAttribute('data-lang');
                syncLangToggles(mode);
            });

            // --- Selection Learning/Ranking Logic ---
            function recordCandidateSelection(word, selectedCandidate) {
                if (!word || !selectedCandidate) return;
                let selections = {};
                try {
                    selections = JSON.parse(safeGetItem('kathaingo_word_selections', '{}')) || {};
                } catch (e) {}
                
                const wordKey = word.toLowerCase();
                if (!selections[wordKey]) {
                    selections[wordKey] = {};
                }
                selections[wordKey][selectedCandidate] = (selections[wordKey][selectedCandidate] || 0) + 1;
                safeSetItem('kathaingo_word_selections', JSON.stringify(selections));
            }

            function getPersonalRankedCandidates(word, candList) {
                if (!candList || candList.length === 0) return candList;
                let selections = {};
                try {
                    selections = JSON.parse(safeGetItem('kathaingo_word_selections', '{}')) || {};
                } catch (e) {}
                
                const wordKey = word.toLowerCase();
                const wordHistory = selections[wordKey];
                if (!wordHistory) return candList;
                
                // Sort based on selection counts in descending order, preserving stable index order
                return [...candList].sort((a, b) => {
                    const countA = wordHistory[a] || 0;
                    const countB = wordHistory[b] || 0;
                    if (countB !== countA) {
                        return countB - countA;
                    }
                    return candList.indexOf(a) - candList.indexOf(b);
                });
            }

            const localConsonants = {
                'ng': { dot: 'ங்', base: 'ங்க' },
                'nj': { dot: 'ஞ்', base: 'ஞ்ச' },
                'ngny': { dot: 'ஞ்', base: 'ஞ்ஞ' },
                'ngy': { dot: 'ஞ்', base: 'ஞ்ஞ' },
                'gny': { dot: 'ஞ்', base: 'ஞ' },
                'ny': { dot: 'ஞ்', base: 'ஞ' },
                'gn': { dot: 'ஞ்', base: 'ஞ' },
                'ndr': { dot: 'ன்ற்', base: 'ன்ற' },
                'ndh': { dot: 'ந்த்', base: 'ந்த' },
                'nd': { dot: 'ந்த்', base: 'ந்த' },
                'th': { dot: 'த்', base: 'த' },
                'zh': { dot: 'ழ்', base: 'ழ' },
                'sh': { dot: 'ஷ்', base: 'ஷ' },
                'ch': { dot: 'ச்', base: 'ச' },
                'kh': { dot: 'க்', base: 'க' },
                'ph': { dot: 'ஃப்', base: 'ஃப' },
                'gh': { dot: 'க்', base: 'க' },
                'lh': { dot: 'ள்', base: 'ள' },
                'dh': { dot: 'த்', base: 'த' },
                'k': { dot: 'க்', base: 'க' },
                'g': { dot: 'க்', base: 'க' },
                'c': { dot: 'ச்', base: 'ச' },
                's': { dot: 'ச்', base: 'ச' },
                'j': { dot: 'ஜ்', base: 'ஜ' },
                't': { dot: 'ட்', base: 'ட' },
                'd': { dot: 'ட்', base: 'ட' },
                'n': { dot: 'ன்', base: 'ன' },
                'p': { dot: 'ப்', base: 'ப' },
                'b': { dot: 'ப்', base: 'ப' },
                'f': { dot: 'ஃப்', base: 'ஃப' },
                'm': { dot: 'ம்', base: 'ம' },
                'y': { dot: 'ய்', base: 'ய' },
                'r': { dot: 'ர்', base: 'ர' },
                'l': { dot: 'ல்', base: 'ல' },
                'v': { dot: 'வ்', base: 'வ' },
                'w': { dot: 'வ்', base: 'வ' },
                'h': { dot: 'ஹ்', base: 'ஹ' },
                'z': { dot: 'ஜ்', base: 'ஜ' },
                'q': { dot: 'ஃ', base: 'ஃ' }
            };

            const localVowels = {
                'aa': { ind: 'ஆ', sign: 'ா' },
                'ee': { ind: 'ஈ', sign: 'ீ' },
                'ea': { ind: 'ஏ', sign: 'ே' },
                'oo': { ind: 'ஊ', sign: 'ூ' },
                'ae': { ind: 'ஏ', sign: 'ே' },
                'ai': { ind: 'ஐ', sign: 'ை' },
                'au': { ind: 'ஔ', sign: 'ௌ' },
                'oa': { ind: 'ஓ', sign: 'ோ' },
                'oh': { ind: 'ஓ', sign: 'ோ' },
                'ou': { ind: 'ஔ', sign: 'ௌ' },
                'ow': { ind: 'ஔ', sign: 'ௌ' },
                'a': { ind: 'அ', sign: '' },
                'i': { ind: 'இ', sign: 'ி' },
                'u': { ind: 'உ', sign: 'ு' },
                'e': { ind: 'எ', sign: 'ெ' },
                'o': { ind: 'ஒ', sign: 'ொ' }
            };

            const localDict = {
                'naan': 'நான்',
                'nan': 'நான்',
                'unga': 'உங்கள்',
                'romba': 'ரொம்ப',
                'nalla': 'நல்லா',
                'ezhuthi': 'எழுதி',
                'eluthi': 'எழுதி',
                'enakku': 'எனக்கு',
                'enaku': 'எனக்கு',
                'pidichirukku': 'பிடிச்சிருக்கு',
                'adei': 'அடேய்',
                'manjakattu': 'மஞ்சக்காட்டு',
                'manjakkattu': 'மஞ்சக்காட்டு',
                'manjakkaattu': 'மஞ்சக்காட்டு',
                'maina': 'மைனா',
                'mainaa': 'மைனா',
                'manina': 'மைனா',
                'maninaa': 'மைனா',
                'ennai': 'என்னை',
                'ennaik': 'என்னைக்',
                'konji': 'கொஞ்சி',
                'konjik': 'கொஞ்சிக்',
                'konjip': 'கொஞ்சிப்',
                'pona': 'போன',
                'ponaa': 'போனா',
                'mustafa': 'முஸ்தஃபா',
                'mustafaa': 'முஸ்தஃபா',
                'mustafah': 'முஸ்தஃபா',
                'musthafaa': 'முஸ்தஃபா',
                'musthafaah': 'முஸ்தஃபா',
                'dont': 'டோன்ட்',
                'vory': 'வொரி',
                'tholan': 'தோழன்',
                'thozhan': 'தோழன்',
                'moolgaatha': 'மூழ்காத',
                'moolgatha': 'மூழ்காத',
                'moozhgaatha': 'மூழ்காத',
                'moozhgaadha': 'மூழ்காத',
                'moolgaada': 'மூழ்காத',
                'moolkaadha': 'மூழ்காத',
                'moolkaatha': 'மூழ்காத',
                'moozhgatha': 'மூழ்காத',
                'moozhgadha': 'மூழ்காத',
                'moozhgada': 'மூழ்காத',
                'moolgada': 'மூழ்காத',
                'friendshippaa': 'ஃப்ரண்ட்ஷிப்பா',
                'frendshippaa': 'ஃப்ரண்ட்ஷிப்பா',
                'frandshippaa': 'ஃப்ரண்ட்ஷிப்பா',
                'friendship': 'ஃப்ரெண்ட்ஷிப்',
                'frendship': 'ஃப்ரெண்ட்ஷிப்',
                'frandship': 'ஃப்ரெண்ட்ஷிப்',
                'kariveppila': 'கறிவேப்பில',
                'kariveppilai': 'கறிவேப்பிலை',
                'karivepila': 'கறிவேப்பில',
                'karivepilai': 'கறிவேப்பிலை',
                'veppila': 'வேப்பில',
                'veppilai': 'வேப்பிலை',
                'vepila': 'வேப்பில',
                'vepilai': 'வேப்பிலை',
                'pena': 'பேனா',
                'penai': 'பேனா',
                'paena': 'பேனா',
                'paenai': 'பேனா',
                'take': 'டேக்',
                'tak': 'டேக்',
                'it': 'இட்',
                'nyayiru': 'ஞாயிறு',
                'gnayiru': 'ஞாயிறு',
                'nyaayiru': 'ஞாயிறு',
                'gnaayiru': 'ஞாயிறு',
                'gnyayiru': 'ஞாயிறு',
                'gnyaayiru': 'ஞாயிறு',
                'nyabagam': 'ஞாபகம்',
                'gnabagam': 'ஞாபகம்',
                'nyaabagam': 'ஞாபகம்',
                'gnaabagam': 'ஞாபகம்',
                'gnyabagam': 'ஞாபகம்',
                'gnyaabagam': 'ஞாபகம்',
                'nyanam': 'ஞானம்',
                'gnanam': 'ஞானம்',
                'nyaanam': 'ஞானம்',
                'gnaanam': 'ஞானம்',
                'gnyanam': 'ஞானம்',
                'gnyaanam': 'ஞானம்',
                'vingyaanam': 'விஞ்ஞானம்',
                'vingyanam': 'விஞ்ஞானம்',
                'vingnyaanam': 'விஞ்ஞானம்',
                'vingnyanam': 'விஞ்ஞானம்',
                'vignyaanam': 'விஞ்ஞானம்',
                'vignanam': 'விஞ்ஞானம்',
                'angyaanam': 'அஞ்ஞானம்',
                'angyanam': 'அஞ்ஞானம்',
                'angnyaanam': 'அஞ்ஞானம்',
                'angnyanam': 'அஞ்ஞானம்',
                'agnyaanam': 'அஞ்ஞானம்',
                'agnanam': 'அஞ்ஞானம்'
            };

            function localTransliterate(word) {
                if (!word) return '';
                const lowercaseWord = word.toLowerCase();
                if (localDict[lowercaseWord]) {
                    return localDict[lowercaseWord];
                }
                
                const len = lowercaseWord.length;
                let i = 0;
                let output = '';
                
                const consonantsKeys = Object.keys(localConsonants).sort((a, b) => b.length - a.length);
                const vowelsKeys = Object.keys(localVowels).sort((a, b) => b.length - a.length);
                
                while (i < len) {
                    let matchedConsonant = null;
                    let consonantLen = 0;
                    
                    for (const key of consonantsKeys) {
                        const kLen = key.length;
                        if (i + kLen <= len && lowercaseWord.substring(i, i + kLen) === key) {
                            matchedConsonant = localConsonants[key];
                            consonantLen = kLen;
                            break;
                        }
                    }
                    
                    if (matchedConsonant) {
                        i += consonantLen;
                        
                        let matchedVowel = null;
                        let vowelLen = 0;
                        
                        for (const key of vowelsKeys) {
                            const kLen = key.length;
                            if (i + kLen <= len && lowercaseWord.substring(i, i + kLen) === key) {
                                matchedVowel = localVowels[key];
                                vowelLen = kLen;
                                break;
                            }
                        }
                        
                        if (matchedVowel) {
                            i += vowelLen;
                            let base;
                            if (consonantLen === 1 && lowercaseWord[i - vowelLen - 1] === 'n' && (i - vowelLen - 1 === 0)) {
                                base = 'ந';
                            } else {
                                base = matchedConsonant.base;
                            }
                            output += base + matchedVowel.sign;
                        } else {
                            if (consonantLen === 1 && lowercaseWord[i - 1] === 'n' && (i - 1 === 0)) {
                                output += 'ந்';
                            } else {
                                output += matchedConsonant.dot;
                            }
                        }
                    } else {
                        let matchedVowel = null;
                        let vowelLen = 0;
                        
                        for (const key of vowelsKeys) {
                            const kLen = key.length;
                            if (i + kLen <= len && lowercaseWord.substring(i, i + kLen) === key) {
                                matchedVowel = localVowels[key];
                                vowelLen = kLen;
                                break;
                            }
                        }
                        
                        if (matchedVowel) {
                            i += vowelLen;
                            output += matchedVowel.ind;
                        } else {
                            output += word[i];
                            i++;
                        }
                    }
                }
                return output;
            }

            let activeInputEl = null;
            let activeTinyMCE = null;
            let activeWordTextNode = null;
            let activeWord = '';
            let activeWordStart = 0;
            let activeWordEnd = 0;
            let candidates = [];
            let selectedIndex = -1;
            let debounceTimer = null;
            const suggestCache = {};
            let mirrorDiv = null;

            // Create global dropdown element
            let dropdownEl = document.getElementById('lang-translit-dropdown');
            if (!dropdownEl) {
                dropdownEl = document.createElement('div');
                dropdownEl.id = 'lang-translit-dropdown';
                dropdownEl.className = 'absolute hidden bg-gray-950/95 border border-gray-800 rounded-2xl shadow-2xl p-1.5 w-56 text-xs font-semibold select-none flex flex-col z-[99999] backdrop-blur-md transition-all duration-150 transform scale-95 opacity-0';
                document.body.appendChild(dropdownEl);
            }

            function getCaretCoordinates(element, position) {
                try {
                    if (!mirrorDiv) {
                        mirrorDiv = document.createElement('div');
                        mirrorDiv.id = 'lang-translit-mirror';
                        mirrorDiv.style.position = 'absolute';
                        mirrorDiv.style.visibility = 'hidden';
                        mirrorDiv.style.left = '-9999px';
                        document.body.appendChild(mirrorDiv);
                    }
                    
                    const div = mirrorDiv;
                    div.innerHTML = '';
                    
                    const style = window.getComputedStyle(element);
                    const properties = [
                        'direction', 'boxSizing', 'width', 'height', 'overflowX', 'overflowY',
                        'borderWidth', 'borderStyle', 'borderColor',
                        'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft',
                        'fontFamily', 'fontSize', 'fontWeight', 'fontStyle', 'fontVariant', 'fontStretch',
                        'lineHeight', 'textTransform', 'wordBreak', 'wordWrap', 'whiteSpace',
                        'letterSpacing', 'textIndent', 'textRendering'
                    ];
                    
                    properties.forEach(prop => {
                        try {
                            if (style[prop] !== undefined) {
                                div.style[prop] = style[prop];
                            }
                        } catch (e) {}
                    });
                    
                    div.style.position = 'absolute';
                    div.style.visibility = 'hidden';
                    div.style.left = '-9999px';
                    
                    const text = element.value.substring(0, position);
                    div.textContent = text;
                    
                    const span = document.createElement('span');
                    span.textContent = element.value.substring(position) || '.';
                    div.appendChild(span);
                    
                    const spanLeft = span.offsetLeft;
                    const spanTop = span.offsetTop;
                    
                    const rect = element.getBoundingClientRect();
                    
                    return {
                        top: rect.top + window.scrollY + spanTop - element.scrollTop,
                        left: rect.left + window.scrollX + spanLeft - element.scrollLeft
                    };
                } catch (err) {
                    console.error('getCaretCoordinates failed, returning fallback rect coordinates:', err);
                    const rect = element.getBoundingClientRect();
                    return {
                        top: rect.bottom + window.scrollY,
                        left: rect.left + window.scrollX
                    };
                }
            }

            function showDropdown(inputEl) {
                if (!candidates || candidates.length === 0) {
                    hideDropdown();
                    return;
                }

                // Build candidates list HTML
                dropdownEl.innerHTML = '';
                
                const listWrapper = document.createElement('div');
                listWrapper.className = 'flex flex-col gap-0.5 max-h-[200px] overflow-y-auto';

                candidates.forEach((cand, idx) => {
                    const optionEl = document.createElement('div');
                    optionEl.className = 'lang-candidate-option flex items-center justify-between px-3.5 py-2 hover:bg-burnt-orange hover:text-white rounded-xl cursor-pointer text-gray-300 transition-colors duration-150 font-bold';
                    if (idx === selectedIndex) {
                        optionEl.classList.add('bg-burnt-orange', 'text-white');
                    }

                    const textSpan = document.createElement('span');
                    textSpan.textContent = (idx + 1) + '. ' + cand;
                    optionEl.appendChild(textSpan);

                    // Sync selection index and highlight visually on mouseenter
                    optionEl.addEventListener('mouseenter', function() {
                        selectedIndex = idx;
                        dropdownEl.querySelectorAll('.lang-candidate-option').forEach((el, index) => {
                            if (index === idx) {
                                el.classList.add('bg-burnt-orange', 'text-white');
                            } else {
                                el.classList.remove('bg-burnt-orange', 'text-white');
                            }
                        });
                    });

                    // Mousedown to prevent input blur and select option instantly
                    optionEl.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectCandidate(idx);
                    });

                    listWrapper.appendChild(optionEl);
                });

                dropdownEl.appendChild(listWrapper);

                // Footer Arrow Buttons for touch screen convenience
                const footerEl = document.createElement('div');
                footerEl.className = 'flex justify-between items-center px-3.5 py-1.5 border-t border-gray-800/80 mt-1.5 text-[9px] text-gray-500 font-bold';
                
                const helpSpan = document.createElement('span');
                helpSpan.textContent = 'Use 1-6 or Space/Enter';
                footerEl.appendChild(helpSpan);

                const btnContainer = document.createElement('div');
                footerEl.appendChild(btnContainer);
                
                const btnUp = document.createElement('button');
                btnUp.type = 'button';
                btnUp.className = 'hover:text-white cursor-pointer bg-transparent border-0 p-0 text-[10px]';
                btnUp.innerHTML = '▲';
                btnUp.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (selectedIndex === -1) {
                        selectedIndex = candidates.length - 1;
                    } else {
                        selectedIndex = (selectedIndex - 1 + candidates.length) % candidates.length;
                    }
                    showDropdown(inputEl);
                });

                const btnDown = document.createElement('button');
                btnDown.type = 'button';
                btnDown.className = 'hover:text-white cursor-pointer bg-transparent border-0 p-0 text-[10px] ml-1.5';
                btnDown.innerHTML = '▼';
                btnDown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (selectedIndex === -1) {
                        selectedIndex = 0;
                    } else {
                        selectedIndex = (selectedIndex + 1) % candidates.length;
                    }
                    showDropdown(inputEl);
                });

                btnContainer.appendChild(btnUp);
                btnContainer.appendChild(btnDown);
                dropdownEl.appendChild(footerEl);

                // Position dropdown
                let coords = { top: 0, left: 0 };
                if (activeTinyMCE) {
                    try {
                        const iframe = activeTinyMCE.getContentAreaContainer().querySelector('iframe') || activeTinyMCE.iframeElement;
                        const iframeRect = iframe.getBoundingClientRect();
                        
                        const range = activeTinyMCE.selection.getRng();
                        var wordRange = range.cloneRange();
                        wordRange.setStart(activeWordTextNode, activeWordStart);
                        wordRange.setEnd(activeWordTextNode, activeWordEnd);
                        const clientRect = wordRange.getBoundingClientRect();
                        
                        coords.top = iframeRect.top + window.scrollY + clientRect.bottom;
                        coords.left = iframeRect.left + window.scrollX + clientRect.left;
                    } catch (err) {
                        console.error('TinyMCE caret coordinates failed:', err);
                        const iframe = activeTinyMCE.getContentAreaContainer().querySelector('iframe') || activeTinyMCE.iframeElement;
                        const iframeRect = iframe.getBoundingClientRect();
                        coords.top = iframeRect.top + window.scrollY + 20;
                        coords.left = iframeRect.left + window.scrollX + 20;
                    }
                } else {
                    const emojiPickerEl = inputEl.closest('.kathaingo-emoji-picker');
                    if (emojiPickerEl) {
                        const pickerRect = emojiPickerEl.getBoundingClientRect();
                        const dropdownWidth = 224; // w-56 is 224px
                        const spacing = 8;
                        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                        
                        let left;
                        if (pickerRect.right + spacing + dropdownWidth <= viewportWidth - 8) {
                            left = pickerRect.right + spacing + window.scrollX;
                        } else {
                            left = pickerRect.left - spacing - dropdownWidth + window.scrollX;
                            if (left < 8) left = 8;
                        }
                        
                        let top = pickerRect.top + window.scrollY;
                        coords = { top, left };
                    } else {
                        coords = getCaretCoordinates(inputEl, activeWordEnd);
                    }
                }

                dropdownEl.style.top = coords.top + 'px';
                dropdownEl.style.left = coords.left + 'px';
                dropdownEl.style.zIndex = '100000';
                
                dropdownEl.classList.remove('hidden');
                dropdownEl.classList.remove('opacity-0', 'scale-95');
                dropdownEl.classList.add('opacity-100', 'scale-100');
            }

            function hideDropdown() {
                dropdownEl.classList.remove('opacity-100', 'scale-100');
                dropdownEl.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    if (dropdownEl.classList.contains('opacity-0')) {
                        dropdownEl.classList.add('hidden');
                    }
                }, 150);
            }

            function selectCandidate(idx) {
                if (activeTinyMCE) {
                    if (!candidates[idx]) return;
                    const replacement = candidates[idx];
                    recordCandidateSelection(activeWord, replacement);
                    
                    var range = activeTinyMCE.selection.getRng();
                    if (activeWordTextNode && activeWordTextNode.ownerDocument === activeTinyMCE.getDoc()) {
                        range.setStart(activeWordTextNode, activeWordStart);
                        range.setEnd(activeWordTextNode, activeWordEnd);
                        activeTinyMCE.selection.setRng(range);
                        activeTinyMCE.insertContent(replacement);
                    }
                    hideDropdown();
                    activeTinyMCE.focus();
                    return;
                }

                if (!activeInputEl || !candidates[idx]) return;
                
                const replacement = candidates[idx];
                recordCandidateSelection(activeWord, replacement);
                
                const text = activeInputEl.value;
                const newText = text.substring(0, activeWordStart) + replacement + text.substring(activeWordEnd);
                activeInputEl.value = newText;
                
                const nextCursorPos = activeWordStart + replacement.length;
                activeInputEl.selectionStart = activeInputEl.selectionEnd = nextCursorPos;
                
                hideDropdown();
                activeInputEl.dispatchEvent(new Event('input'));
                activeInputEl.focus();
            }

            function checkActiveWord(inputEl) {
                if (currentInputMode !== 'ta') {
                    hideDropdown();
                    return;
                }

                activeTinyMCE = null;
                activeWordTextNode = null;
                activeInputEl = inputEl;
                const cursor = inputEl.selectionStart;
                const textBeforeCursor = inputEl.value.substring(0, cursor);
                
                const match = textBeforeCursor.match(/([a-zA-Z']+)$/);
                
                if (match) {
                    activeWord = match[1];
                    activeWordStart = cursor - activeWord.length;
                    activeWordEnd = cursor;
                    
                    if (/[\u0B80-\u0BFF]/.test(activeWord)) {
                        hideDropdown();
                        return;
                    }

                    if (activeWord.length >= 2) {
                        const localCand = localTransliterate(activeWord);
                        if (localCand && localCand !== activeWord) {
                            candidates = [localCand, activeWord];
                            selectedIndex = -1;
                            showDropdown(inputEl);
                        }

                        const cached = suggestCache[activeWord];
                        if (cached) {
                            if (debounceTimer) clearTimeout(debounceTimer);
                            candidates = getPersonalRankedCandidates(activeWord, cached);
                            selectedIndex = -1;
                            showDropdown(inputEl);
                            return;
                        }

                        if (debounceTimer) clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            fetch(suggestUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ word: activeWord })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.candidates && data.candidates.length > 0) {
                                    suggestCache[activeWord] = data.candidates;
                                    
                                    if (inputEl.selectionStart === activeWordEnd) {
                                        candidates = getPersonalRankedCandidates(activeWord, data.candidates);
                                        selectedIndex = -1;
                                        showDropdown(inputEl);
                                    }
                                } else {
                                    if (!candidates || candidates.length === 0) {
                                        hideDropdown();
                                    }
                                }
                            })
                            .catch(err => {
                                console.error('Dropdown fetch error:', err);
                                if (!candidates || candidates.length === 0) {
                                    hideDropdown();
                                }
                            });
                        }, 150);
                    } else {
                        hideDropdown();
                    }
                } else {
                    hideDropdown();
                }
            }

            function checkActiveWordTinyMCE(editor) {
                if (currentInputMode !== 'ta') {
                    hideDropdown();
                    return;
                }

                activeInputEl = null;
                activeTinyMCE = editor;
                
                const range = editor.selection.getRng();
                if (range.collapsed && range.startContainer.nodeType === 3) {
                    const text = range.startContainer.data;
                    const offset = range.startOffset;
                    const textBeforeCursor = text.substring(0, offset);
                    
                    const match = textBeforeCursor.match(/([a-zA-Z']+)$/);
                    if (match) {
                        activeWord = match[1];
                        activeWordStart = offset - activeWord.length;
                        activeWordEnd = offset;
                        activeWordTextNode = range.startContainer;
                        
                        if (/[\u0B80-\u0BFF]/.test(activeWord)) {
                            hideDropdown();
                            return;
                        }

                        if (activeWord.length >= 2) {
                            const localCand = localTransliterate(activeWord);
                            if (localCand && localCand !== activeWord) {
                                candidates = [localCand, activeWord];
                                selectedIndex = -1;
                                showDropdown(null);
                            }

                            const cached = suggestCache[activeWord];
                            if (cached) {
                                if (debounceTimer) clearTimeout(debounceTimer);
                                candidates = getPersonalRankedCandidates(activeWord, cached);
                                selectedIndex = -1;
                                showDropdown(null);
                                return;
                            }

                            if (debounceTimer) clearTimeout(debounceTimer);
                            debounceTimer = setTimeout(() => {
                                fetch(suggestUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: JSON.stringify({ word: activeWord })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success && data.candidates && data.candidates.length > 0) {
                                        suggestCache[activeWord] = data.candidates;
                                        
                                        const curRange = editor.selection.getRng();
                                        if (curRange.startContainer === activeWordTextNode && curRange.startOffset === activeWordEnd) {
                                            candidates = getPersonalRankedCandidates(activeWord, data.candidates);
                                            selectedIndex = -1;
                                            showDropdown(null);
                                        }
                                    } else {
                                        if (!candidates || candidates.length === 0) {
                                            hideDropdown();
                                        }
                                    }
                                })
                                .catch(err => {
                                    console.error('Dropdown fetch error:', err);
                                    if (!candidates || candidates.length === 0) {
                                        hideDropdown();
                                    }
                                });
                            }, 150);
                        } else {
                            hideDropdown();
                        }
                    } else {
                        hideDropdown();
                    }
                } else {
                    hideDropdown();
                }
            }

            function isTransliteratable(element) {
                if (!element || typeof element.matches !== 'function') return false;
                return element.matches(inputSelector) || 
                       element.hasAttribute('data-kathaingo-transliterate') || 
                       element.classList.contains('kathaingo-transliterate');
            }

            // Event Listeners for inputs using delegated events
            document.addEventListener('input', function(event) {
                if (!isTransliteratable(event.target)) return;
                checkActiveWord(event.target);
            });

            document.addEventListener('click', function(event) {
                if (dropdownEl && !dropdownEl.contains(event.target) && !isTransliteratable(event.target)) {
                    hideDropdown();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (!isTransliteratable(event.target)) return;
                
                const inputEl = event.target;
                const isDropdownOpen = !dropdownEl.classList.contains('hidden') && dropdownEl.classList.contains('opacity-100');

                if (currentInputMode === 'ta' && event.key === ' ') {
                    const cursor = inputEl.selectionStart;
                    const textBeforeCursor = inputEl.value.substring(0, cursor);
                    const match = textBeforeCursor.match(/([a-zA-Z']+)$/);
                    if (match) {
                        const word = match[1];
                        if (word.length >= 2 && !/[\u0B80-\u0BFF]/.test(word)) {
                            event.preventDefault();
                            const wordStart = cursor - word.length;
                            const wordEnd = cursor;
                            
                            let replacement = '';
                            if (isDropdownOpen && candidates && candidates.length > 0) {
                                const idx = selectedIndex !== -1 ? selectedIndex : 0;
                                replacement = candidates[idx] || localTransliterate(word);
                            } else {
                                replacement = localTransliterate(word);
                            }
                            
                            recordCandidateSelection(word, replacement);
                            
                            const text = inputEl.value;
                            const newText = text.substring(0, wordStart) + replacement + ' ' + text.substring(wordEnd);
                            inputEl.value = newText;
                            
                            const nextCursorPos = wordStart + replacement.length + 1;
                            inputEl.selectionStart = inputEl.selectionEnd = nextCursorPos;
                            
                            hideDropdown();
                            inputEl.dispatchEvent(new Event('input'));
                            inputEl.focus();
                            return;
                        }
                    }
                }

                if (isDropdownOpen) {
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        if (selectedIndex === -1) {
                            selectedIndex = 0;
                        } else {
                            selectedIndex = (selectedIndex + 1) % candidates.length;
                        }
                        showDropdown(inputEl);
                    }
                    else if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        if (selectedIndex === -1) {
                            selectedIndex = candidates.length - 1;
                        } else {
                            selectedIndex = (selectedIndex - 1 + candidates.length) % candidates.length;
                        }
                        showDropdown(inputEl);
                    }
                    else if (event.key === 'Enter' || event.key === 'Tab') {
                        if (selectedIndex !== -1) {
                            event.preventDefault();
                            selectCandidate(selectedIndex);
                        } else {
                            if (candidates && candidates[0]) {
                                event.preventDefault();
                                selectCandidate(0);
                            } else {
                                hideDropdown();
                            }
                        }
                    }
                    else if (event.key === 'Escape') {
                        event.preventDefault();
                        hideDropdown();
                    }
                    else if (event.key >= '1' && event.key <= '6') {
                        const idx = parseInt(event.key) - 1;
                        if (candidates[idx]) {
                            event.preventDefault();
                            selectCandidate(idx);
                        }
                    }
                }
            });

            // Expose a function to bind the helper to TinyMCE editors
            window.KathaingoLanguageHelper.bindTinyMCE = function (editor) {
                editor.on('input keyup selectionchange', function(e) {
                    if (e.type === 'keyup') {
                        if (['ArrowUp', 'ArrowDown', 'Enter', 'Tab', 'Escape', '1', '2', '3', '4', '5', '6', ' '].includes(e.key)) {
                            return;
                        }
                    }
                    checkActiveWordTinyMCE(editor);
                });

                editor.on('click', function() {
                    hideDropdown();
                });

                editor.on('keydown', function(event) {
                    const isDropdownOpen = !dropdownEl.classList.contains('hidden') && dropdownEl.classList.contains('opacity-100');
                    
                    if (currentInputMode === 'ta' && event.key === ' ') {
                        const range = editor.selection.getRng();
                        if (range.collapsed && range.startContainer.nodeType === 3) {
                            const text = range.startContainer.data;
                            const offset = range.startOffset;
                            const textBeforeCursor = text.substring(0, offset);
                            const match = textBeforeCursor.match(/([a-zA-Z']+)$/);
                            if (match) {
                                const word = match[1];
                                if (word.length >= 2 && !/[\u0B80-\u0BFF]/.test(word)) {
                                    event.preventDefault();
                                    
                                    let replacement = '';
                                    if (isDropdownOpen && candidates && candidates.length > 0) {
                                        const idx = selectedIndex !== -1 ? selectedIndex : 0;
                                        replacement = candidates[idx] || localTransliterate(word);
                                    } else {
                                        replacement = localTransliterate(word);
                                    }
                                    
                                    recordCandidateSelection(word, replacement);
                                    
                                    const start = offset - word.length;
                                    range.setStart(range.startContainer, start);
                                    range.setEnd(range.startContainer, offset);
                                    editor.selection.setRng(range);
                                    
                                    editor.insertContent(replacement + ' ');
                                    hideDropdown();
                                    return;
                                }
                            }
                        }
                    }

                    if (isDropdownOpen) {
                        if (event.key === 'ArrowDown') {
                            event.preventDefault();
                            if (selectedIndex === -1) {
                                selectedIndex = 0;
                            } else {
                                selectedIndex = (selectedIndex + 1) % candidates.length;
                            }
                            showDropdown(null);
                        }
                        else if (event.key === 'ArrowUp') {
                            event.preventDefault();
                            if (selectedIndex === -1) {
                                selectedIndex = candidates.length - 1;
                            } else {
                                selectedIndex = (selectedIndex - 1 + candidates.length) % candidates.length;
                            }
                            showDropdown(null);
                        }
                        else if (event.key === 'Enter' || event.key === 'Tab') {
                            if (selectedIndex !== -1) {
                                event.preventDefault();
                                selectCandidate(selectedIndex);
                            } else {
                                if (candidates && candidates[0]) {
                                    event.preventDefault();
                                    selectCandidate(0);
                                } else {
                                    hideDropdown();
                                }
                            }
                        }
                        else if (event.key === 'Escape') {
                            event.preventDefault();
                            hideDropdown();
                        }
                        else if (event.key >= '1' && event.key <= '6') {
                            const idx = parseInt(event.key) - 1;
                            if (candidates[idx]) {
                                event.preventDefault();
                                selectCandidate(idx);
                            }
                        }
                    }
                });
            };
        }
    };
})();
