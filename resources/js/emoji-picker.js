const emojiList = [
    // 1. Smileys
    { emoji: '😊', category: 'Smileys', aliases: ['smile', 'happy', 'joy', 'grin', 'face', 'சிரிப்பு', 'மகிழ்ச்சி', 'sirippu'] },
    { emoji: '😂', category: 'Smileys', aliases: ['laugh', 'laughter', 'funny', 'comedy', 'சிரிப்பு', 'சிரிச்சேன்', 'sirippu', 'sirichen', 'tear', 'haha', 'joy'] },
    { emoji: '🤣', category: 'Smileys', aliases: ['rofl', 'laugh', 'funny', 'rolling', 'சிரிப்பு', 'sirippu'] },
    { emoji: '🙂', category: 'Smileys', aliases: ['smile', 'slight', 'happy', 'சிரிப்பு'] },
    { emoji: '😉', category: 'Smileys', aliases: ['wink', 'blink', 'happy', 'சிரிப்பு'] },
    { emoji: '😍', category: 'Smileys', aliases: ['love', 'heart eyes', 'face', 'காதல்', 'அன்பு', 'kadhal', 'anbu'] },
    { emoji: '🥰', category: 'Smileys', aliases: ['love', 'hearts', 'face', 'happy', 'காதல்', 'அன்பு', 'kadhal', 'anbu'] },
    { emoji: '😘', category: 'Smileys', aliases: ['kiss', 'heart', 'blow', 'love', 'முத்தம்', 'காதல்', 'அன்பு'] },
    { emoji: '😋', category: 'Smileys', aliases: ['yum', 'delicious', 'tongue', 'face', 'சுவை'] },
    { emoji: '😎', category: 'Smileys', aliases: ['cool', 'sunglasses', 'sun', 'face', 'கெத்து'] },
    { emoji: '🤔', category: 'Smileys', aliases: ['think', 'thought', 'question', 'யோசனை', 'கேள்வி', 'yosanai', 'kelvi'] },
    { emoji: '🤨', category: 'Smileys', aliases: ['raised eyebrow', 'question', 'doubt', 'சந்தேகம்', 'கேள்வி'] },
    { emoji: '😐', category: 'Smileys', aliases: ['neutral', 'meh', 'flat face'] },
    { emoji: '😒', category: 'Smileys', aliases: ['unamused', 'meh', 'face'] },
    { emoji: '🙄', category: 'Smileys', aliases: ['roll eyes', 'face', 'meh'] },
    { emoji: '😔', category: 'Smileys', aliases: ['sad', 'pensive', 'regret', 'sorrow', 'சோகம்', 'வருத்தம்', 'sogam'] },
    { emoji: '😢', category: 'Smileys', aliases: ['cry', 'sad', 'tear', 'unhappy', 'face', 'அழுகை', 'சோகம்', 'sogam', 'alugai'] },
    { emoji: '😴', category: 'Smileys', aliases: ['sleep', 'sleepy', 'tired', 'zzz', 'தூக்கம்', 'thookam'] },
    { emoji: '😷', category: 'Smileys', aliases: ['mask', 'sick', 'doctor', 'cold', 'நோய்'] },
    { emoji: '🤯', category: 'Smileys', aliases: ['explode', 'mind', 'blow', 'shock', 'அதிர்ச்சி'] },
    { emoji: '🥳', category: 'Smileys', aliases: ['party', 'celebrate', 'horn', 'hat', 'கொண்டாட்டம்'] },

    // 2. Love & Care
    { emoji: '❤️', category: 'Love & Care', aliases: ['love', 'heart', 'அன்பு', 'காதல்', 'anbu', 'kadhal'] },
    { emoji: '💖', category: 'Love & Care', aliases: ['love', 'sparkle', 'heart', 'pink', 'காதல்', 'அன்பு'] },
    { emoji: '💗', category: 'Love & Care', aliases: ['love', 'grow', 'heart', 'pink', 'காதல்', 'அன்பு'] },
    { emoji: '💓', category: 'Love & Care', aliases: ['love', 'beat', 'heart', 'pink', 'காதல்', 'அன்பு'] },
    { emoji: '💕', category: 'Love & Care', aliases: ['love', 'double hearts', 'pink', 'காதல்', 'அன்பு'] },
    { emoji: '💞', category: 'Love & Care', aliases: ['love', 'revolving hearts', 'pink', 'காதல்', 'அன்பு'] },
    { emoji: '💘', category: 'Love & Care', aliases: ['love', 'arrow heart', 'காதல்', 'அன்பு'] },
    { emoji: '💝', category: 'Love & Care', aliases: ['love', 'gift', 'ribbon', 'heart', 'காதல்', 'அன்பு'] },
    { emoji: '💔', category: 'Love & Care', aliases: ['broken heart', 'love', 'sad', 'சோகம்'] },
    { emoji: '❣️', category: 'Love & Care', aliases: ['exclamation heart', 'red', 'அன்பு'] },
    { emoji: '🤗', category: 'Love & Care', aliases: ['hug', 'care', 'open hands', 'அரவணைப்பு'] },
    { emoji: '🙏', category: 'Love & Care', aliases: ['thanks', 'thank you', 'prayer', 'please', 'நன்றி', 'வணக்கம்', 'nandri', 'vanakkam'] },
    { emoji: '🤝', category: 'Love & Care', aliases: ['handshake', 'deal', 'agree', 'partner', 'ஒப்பந்தம்'] },
    { emoji: '👥', category: 'Love & Care', aliases: ['users', 'people', 'group', 'community', 'மக்கள்', 'குழு'] },
    { emoji: '🫂', category: 'Love & Care', aliases: ['hug', 'huggers', 'support', 'care', 'அரவணைப்பு'] },

    // 3. Celebration
    { emoji: '🎉', category: 'Celebration', aliases: ['congrats', 'celebration', 'wishes', 'வாழ்த்துக்கள்', 'kondattam', 'vaazhthukkal'] },
    { emoji: '🎊', category: 'Celebration', aliases: ['party', 'ball', 'celebrate', 'வாழ்த்துக்கள்', 'கொண்டாட்டம்'] },
    { emoji: '🎈', category: 'Celebration', aliases: ['balloon', 'red', 'celebrate', 'பலூன்'] },
    { emoji: '🎁', category: 'Celebration', aliases: ['gift', 'present', 'box', 'wrap', 'பரிசு'] },
    { emoji: '🎂', category: 'Celebration', aliases: ['cake', 'birthday', 'candle', 'sweet', 'பிறந்தநாள்'] },
    { emoji: '✨', category: 'Celebration', aliases: ['sparkle', 'star', 'shine', 'celebrate', 'மின்னல்'] },
    { emoji: '🌟', category: 'Celebration', aliases: ['star', 'glow', 'shine', 'yellow', 'நட்சத்திரம்'] },
    { emoji: '⭐', category: 'Celebration', aliases: ['star', 'yellow', 'நட்சத்திரம்', 'star'] },
    { emoji: '💥', category: 'Celebration', aliases: ['collision', 'explosion', 'bang', 'வெடி'] },
    { emoji: '🏆', category: 'Celebration', aliases: ['trophy', 'win', 'prize', 'first', 'வெற்றி', 'கோப்பை'] },

    // 4. Thought & Reaction
    { emoji: '👍', category: 'Thought & Reaction', aliases: ['thumbs up', 'like', 'yes', 'okay', 'ok', 'நன்று', 'சரி'] },
    { emoji: '👎', category: 'Thought & Reaction', aliases: ['thumbs down', 'dislike', 'no', 'இல்லை'] },
    { emoji: '👏', category: 'Thought & Reaction', aliases: ['clap', 'applaud', 'hands', 'கைதட்டல்', 'பாராட்டு'] },
    { emoji: '🙌', category: 'Thought & Reaction', aliases: ['hooray', 'celebrate', 'hands', 'வாழ்த்து'] },
    { emoji: '💡', category: 'Thought & Reaction', aliases: ['light bulb', 'idea', 'brain', 'thought', 'ஒளி', 'யோசனை'] },
    { emoji: '🧠', category: 'Thought & Reaction', aliases: ['brain', 'mind', 'think', 'logic', 'மூளை', 'அறிவு'] },
    { emoji: '🧐', category: 'Thought & Reaction', aliases: ['monocle', 'look', 'investigate', 'search', 'ஆராய்ச்சி'] },
    { emoji: '💬', category: 'Thought & Reaction', aliases: ['speech bubble', 'talk', 'comment', 'பேச்சு', 'கருத்து'] },
    { emoji: '🗯️', category: 'Thought & Reaction', aliases: ['anger bubble', 'comment', 'scream', 'கோபம்'] },
    { emoji: '🤷', category: 'Thought & Reaction', aliases: ['shrug', 'shrugger', 'metadata', 'தெரியாது'] },
    { emoji: '🤦', category: 'Thought & Reaction', aliases: ['facepalm', 'face palm', 'omg', 'அய்யோ'] },
    { emoji: '📷', category: 'Thought & Reaction', aliases: ['camera', 'photo', 'picture', 'capture', 'படம்', 'கேமரா'] },

    // 5. Nature
    { emoji: '🌸', category: 'Nature', aliases: ['cherry blossom', 'flower', 'spring', 'pink', 'பூ', 'மலர்'] },
    { emoji: '🌹', category: 'Nature', aliases: ['rose', 'red', 'flower', 'love', 'ரோஜா', 'பூ'] },
    { emoji: '🌺', category: 'Nature', aliases: ['hibiscus', 'flower', 'orange', 'pink', 'செம்பருத்தி', 'பூ', 'மலர்'] },
    { emoji: '🌻', category: 'Nature', aliases: ['sunflower', 'yellow', 'flower', 'சூரியகாந்தி', 'பூ'] },
    { emoji: '🌼', category: 'Nature', aliases: ['blossom', 'flower', 'yellow', 'பூ'] },
    { emoji: '🌷', category: 'Nature', aliases: ['tulip', 'flower', 'pink', 'பூ'] },
    { emoji: '🌱', category: 'Nature', aliases: ['seedling', 'plant', 'grow', 'green', 'leaf', 'செடி'] },
    { emoji: '🍀', category: 'Nature', aliases: ['four leaf clover', 'luck', 'green', 'அதிர்ஷ்டம்'] },
    { emoji: '🌴', category: 'Nature', aliases: ['palm tree', 'beach', 'travel', 'மரம்'] },
    { emoji: '🔥', category: 'Nature', aliases: ['fire', 'flame', 'hot', 'cook', 'spark', 'நெருப்பு', 'தணல்'] },
    { emoji: '🌈', category: 'Nature', aliases: ['rainbow', 'sky', 'rain', 'color', 'வானவில்'] },
    { emoji: '❄️', category: 'Nature', aliases: ['snowflake', 'snow', 'winter', 'cold', 'பனி'] },

    // 6. Travel
    { emoji: '🚗', category: 'Travel', aliases: ['car', 'red', 'drive', 'transport', 'travel', 'கார்', 'வண்டி'] },
    { emoji: '🚲', category: 'Travel', aliases: ['bike', 'bicycle', 'travel', 'cycle', 'சைக்கிள்'] },
    { emoji: '✈️', category: 'Travel', aliases: ['airplane', 'plane', 'fly', 'airport', 'travel', 'விமானம்'] },
    { emoji: '🚀', category: 'Travel', aliases: ['rocket', 'space', 'fly', 'launch', 'travel', 'ராக்கெட்'] },
    { emoji: '🛸', category: 'Travel', aliases: ['ufo', 'alien', 'space', 'travel', 'பறக்கும் தட்டு'] },
    { emoji: '🚢', category: 'Travel', aliases: ['ship', 'boat', 'water', 'sea', 'travel', 'கப்பல்'] },
    { emoji: '🗺️', category: 'Travel', aliases: ['map', 'travel', 'location', 'direction', 'வரைபடம்'] },
    { emoji: '🧭', category: 'Travel', aliases: ['compass', 'travel', 'direct', 'navigation', 'திசைகாட்டி'] },
    { emoji: '🏕️', category: 'Travel', aliases: ['camping', 'camp', 'tent', 'travel', 'nature', 'கூடாரம்'] },
    { emoji: '🏖️', category: 'Travel', aliases: ['beach umbrella', 'sand', 'travel', 'sea', 'கடற்கரை'] },
    { emoji: '🌍', category: 'Travel', aliases: ['earth', 'globe', 'world', 'international', 'travel', 'உலகம்', 'பூமி'] },

    // 7. Food
    { emoji: '🍎', category: 'Food', aliases: ['apple', 'red', 'fruit', 'food', 'ஆப்பிள்'] },
    { emoji: '🍌', category: 'Food', aliases: ['banana', 'yellow', 'fruit', 'food', 'வாழைப்பழம்'] },
    { emoji: '🍓', category: 'Food', aliases: ['strawberry', 'red', 'fruit', 'food', 'பழம்'] },
    { emoji: '🍉', category: 'Food', aliases: ['watermelon', 'fruit', 'food', 'தர்பூசணி'] },
    { emoji: '🍔', category: 'Food', aliases: ['hamburger', 'burger', 'cheese', 'meat', 'food', 'உணவு'] },
    { emoji: '🍕', category: 'Food', aliases: ['pizza', 'cheese', 'slice', 'food', 'உணவு'] },
    { emoji: '🍰', category: 'Food', aliases: ['cake slice', 'sweet', 'dessert', 'food', 'இனிப்பு'] },
    { emoji: '🍪', category: 'Food', aliases: ['cookie', 'sweet', 'chocolate', 'food', 'பிஸ்கட்'] },
    { emoji: '☕', category: 'Food', aliases: ['coffee', 'hot', 'tea', 'drink', 'food', 'காபி', 'தேநீர்'] },
    { emoji: '🥤', category: 'Food', aliases: ['soda', 'cup', 'straw', 'drink', 'food', 'பானம்'] },

    // 8. Writing & Books
    { emoji: '✍️', category: 'Writing & Books', aliases: ['write', 'writing', 'pen', 'எழுத', 'எழுத்து', 'eluthu', 'ezhuthu'] },
    { emoji: '📝', category: 'Writing & Books', aliases: ['memo', 'pencil', 'writing', 'note book', 'குறிப்பு'] },
    { emoji: '✏️', category: 'Writing & Books', aliases: ['pencil', 'writing', 'note', 'பென்சில்'] },
    { emoji: '🖋️', category: 'Writing & Books', aliases: ['fountain pen', 'writing', 'note', 'பேனா'] },
    { emoji: '🖊️', category: 'Writing & Books', aliases: ['ballpoint pen', 'writing', 'note', 'பேனா'] },
    { emoji: '📚', category: 'Writing & Books', aliases: ['books', 'library', 'read', 'education', 'book', 'புத்தகங்கள்', 'நூலகம்'] },
    { emoji: '📖', category: 'Writing & Books', aliases: ['book', 'read', 'story', 'கதை', 'புத்தகம்', 'vasippu', 'kathai', 'puthagam'] },
    { emoji: '📕', category: 'Writing & Books', aliases: ['red book', 'read', 'புத்தகம்'] },
    { emoji: '📘', category: 'Writing & Books', aliases: ['blue book', 'read', 'புத்தகம்'] },
    { emoji: '📙', category: 'Writing & Books', aliases: ['orange book', 'read', 'புத்தகம்'] },

    // 9. Symbols
    { emoji: '⚠️', category: 'Symbols', aliases: ['warning', 'alert', 'danger', 'sign', 'yellow', 'எச்சரிக்கை'] },
    { emoji: '🚫', category: 'Symbols', aliases: ['prohibited', 'ban', 'block', 'sign', 'red', 'தடை'] },
    { emoji: '💯', category: 'Symbols', aliases: ['hundred points', 'score', 'perfect', 'நூறு'] },
    { emoji: '✅', category: 'Symbols', aliases: ['right', 'correct', 'yes', 'okay', 'ok', 'tick', 'accept', 'done', 'சரி', 'ஆம்', 'ஓகே', 'sari', 'seri', 'aam'] },
    { emoji: '❌', category: 'Symbols', aliases: ['wrong', 'incorrect', 'no', 'reject', 'cancel', 'cross', 'mistake', 'தவறு', 'இல்லை', 'thavaru', 'thappu', 'illai', 'vendaam'] },
    { emoji: '❓', category: 'Symbols', aliases: ['question', 'doubt', 'why', 'confusion', 'கேள்வி', 'சந்தேகம்', 'kelvi', 'santhegam'] },
    { emoji: '➕', category: 'Symbols', aliases: ['plus sign', 'math', 'கூட்டல்'] },
    { emoji: '➖', category: 'Symbols', aliases: ['minus sign', 'math', 'கழித்தல்'] }
];

const starterSet = [
    '😊', '❤️', '😂', '🙏', '🎉', '✅', '❌', '❓', '✍️', '📖', 
    '🌺', '⭐', '🔥', '👏', '🤔', '😢', '🤗', '🏆', '📷', '🌍'
];

let pickerEl = null;
let currentTargetButton = null;
let activeEditor = null;
let currentCategory = 'Starter';

function getRecentEmojis() {
    try {
        return JSON.parse(localStorage.getItem('kathaingo-recent-emojis') || '[]');
    } catch (e) {
        return [];
    }
}

function saveRecentEmoji(emoji) {
    let recent = getRecentEmojis();
    // Remove if already exists
    recent = recent.filter(item => item !== emoji);
    // Add to top
    recent.unshift(emoji);
    // Keep max 14
    recent = recent.slice(0, 14);
    try {
        localStorage.setItem('kathaingo-recent-emojis', JSON.stringify(recent));
    } catch (e) {}
}

function createPickerDOM() {
    pickerEl = document.createElement('div');
    pickerEl.className = 'kathaingo-emoji-picker';
    pickerEl.style.display = 'none';

    // Header & Search
    const header = document.createElement('div');
    header.className = 'kathaingo-emoji-header';
    header.style.display = 'flex';
    header.style.alignItems = 'center';
    header.style.gap = '8px';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Emoji தேடுக... sari, love, book';
    searchInput.className = 'kathaingo-emoji-search';
    searchInput.style.flex = '1';
    searchInput.setAttribute('data-kathaingo-transliterate', 'true');
    
    searchInput.addEventListener('input', (e) => {
        handleSearch(e.target.value);
    });

    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const transDropdown = document.getElementById('lang-translit-dropdown');
            const isTransOpen = transDropdown && !transDropdown.classList.contains('hidden');
            if (isTransOpen) {
                // Let language-helper select the suggestion
                return;
            }
            // If suggestions are closed, select the first matching emoji in the grid
            const firstEmojiBtn = pickerEl.querySelector('.kathaingo-emoji-btn');
            if (firstEmojiBtn) {
                e.preventDefault();
                firstEmojiBtn.click();
            }
        }
    });
    
    // Transliteration En / த Toggle Button
    const toggleContainer = document.createElement('div');
    toggleContainer.className = 'kathaingo-emoji-toggle';
    toggleContainer.style.display = 'flex';
    toggleContainer.style.alignItems = 'center';
    toggleContainer.style.backgroundColor = '#0f172a';
    toggleContainer.style.border = '1px solid #334155';
    toggleContainer.style.borderRadius = '9999px';
    toggleContainer.style.padding = '2px';
    toggleContainer.style.flexShrink = '0';
    toggleContainer.title = 'Transliteration Mode';

    const btnEn = document.createElement('button');
    btnEn.type = 'button';
    btnEn.setAttribute('data-lang', 'en');
    btnEn.textContent = 'En';

    const btnTa = document.createElement('button');
    btnTa.type = 'button';
    btnTa.setAttribute('data-lang', 'ta');
    btnTa.textContent = 'த';

    let activeMode = 'en';
    try {
        activeMode = localStorage.getItem('kathaingo_input_mode') || 'en';
    } catch (err) {}

    const btnBaseClass = 'lang-toggle-btn px-1.5 py-0.5 rounded-full text-[10px] font-bold transition-all duration-200 cursor-pointer';
    if (activeMode === 'en') {
        btnEn.className = `${btnBaseClass} bg-burnt-orange text-white`;
        btnTa.className = `${btnBaseClass} bg-transparent text-gray-400 hover:text-white`;
    } else {
        btnTa.className = `${btnBaseClass} bg-burnt-orange text-white`;
        btnEn.className = `${btnBaseClass} bg-transparent text-gray-400 hover:text-white`;
    }

    toggleContainer.appendChild(btnEn);
    toggleContainer.appendChild(btnTa);
    
    header.appendChild(searchInput);
    header.appendChild(toggleContainer);
    pickerEl.appendChild(header);

    // Navigation Category Icons
    const nav = document.createElement('div');
    nav.className = 'kathaingo-emoji-categories-nav';
    
    const categories = [
        { id: 'Starter', icon: '✨', title: 'Starter Set / ஆரம்பத் தொகுப்பு' },
        { id: 'Recent', icon: '⏱️', title: 'Recently Used' },
        { id: 'Smileys', icon: '😊', title: 'Smileys' },
        { id: 'Love & Care', icon: '❤️', title: 'Love & Care' },
        { id: 'Celebration', icon: '🎉', title: 'Celebration' },
        { id: 'Thought & Reaction', icon: '🤔', title: 'Thought & Reaction' },
        { id: 'Nature', icon: '🌸', title: 'Nature' },
        { id: 'Travel', icon: '🚗', title: 'Travel' },
        { id: 'Food', icon: '🍔', title: 'Food' },
        { id: 'Writing & Books', icon: '📚', title: 'Writing & Books' },
        { id: 'Symbols', icon: '⚠️', title: 'Symbols' }
    ];

    categories.forEach(cat => {
        const span = document.createElement('span');
        span.className = 'category-nav-btn';
        span.textContent = cat.icon;
        span.title = cat.title;
        span.dataset.category = cat.id;
        
        span.addEventListener('click', () => {
            searchInput.value = ''; // clear search
            switchCategory(cat.id);
        });
        
        nav.appendChild(span);
    });
    
    pickerEl.appendChild(nav);

    // Scrollable Grid Container
    const gridContainer = document.createElement('div');
    gridContainer.className = 'kathaingo-emoji-scroll-container';
    pickerEl.appendChild(gridContainer);

    document.body.appendChild(pickerEl);
}

function switchCategory(catId) {
    currentCategory = catId;
    
    // Update active nav state
    const navBtns = pickerEl.querySelectorAll('.category-nav-btn');
    navBtns.forEach(btn => {
        if (btn.dataset.category === catId) {
            btn.classList.add('category-nav-btn--active');
        } else {
            btn.classList.remove('category-nav-btn--active');
        }
    });

    const grid = pickerEl.querySelector('.kathaingo-emoji-scroll-container');
    grid.innerHTML = '';

    let emojisToRender = [];
    if (catId === 'Starter') {
        emojisToRender = starterSet.map(emo => {
            const found = emojiList.find(item => item.emoji === emo);
            return found || { emoji: emo, category: 'Starter', aliases: [] };
        });
    } else if (catId === 'Recent') {
        const recent = getRecentEmojis();
        if (recent.length === 0) {
            const emptyNotice = document.createElement('div');
            emptyNotice.className = 'kathaingo-emoji-empty';
            emptyNotice.textContent = 'No recently used emojis / சமீபத்திய இமோஜிகள் இல்லை';
            grid.appendChild(emptyNotice);
            return;
        }
        emojisToRender = recent.map(emo => {
            const found = emojiList.find(item => item.emoji === emo);
            return found || { emoji: emo };
        });
    } else {
        emojisToRender = emojiList.filter(item => item.category === catId);
    }

    renderEmojiGrid(emojisToRender, grid);
}

function renderEmojiGrid(emojis, container) {
    const listDiv = document.createElement('div');
    listDiv.className = 'kathaingo-emoji-grid';

    emojis.forEach(item => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'kathaingo-emoji-btn';
        btn.textContent = item.emoji;
        
        btn.addEventListener('click', () => {
            insertEmoji(item.emoji);
        });
        
        listDiv.appendChild(btn);
    });

    container.appendChild(listDiv);
}

function handleSearch(query) {
    const grid = pickerEl.querySelector('.kathaingo-emoji-scroll-container');
    grid.innerHTML = '';

    // Clear active nav state since we are searching
    const navBtns = pickerEl.querySelectorAll('.category-nav-btn');
    navBtns.forEach(btn => btn.classList.remove('category-nav-btn--active'));

    if (query.trim() === '') {
        switchCategory(currentCategory);
        return;
    }

    const lowerQuery = query.toLowerCase().trim();

    // Score emojis based on match level
    const scored = emojiList.map(item => {
        let score = 0;
        const aliases = item.aliases || [];
        
        for (let i = 0; i < aliases.length; i++) {
            const alias = aliases[i].toLowerCase().trim();
            if (alias === lowerQuery) {
                score = Math.max(score, 100);
            } else if (alias.startsWith(lowerQuery)) {
                score = Math.max(score, 50);
            } else if (alias.includes(lowerQuery)) {
                score = Math.max(score, 10);
            }
        }
        
        return { item, score };
    }).filter(res => res.score > 0);

    // Sort: highest score first
    scored.sort((a, b) => b.score - a.score);

    const filtered = scored.map(res => res.item);

    if (filtered.length === 0) {
        const emptyNotice = document.createElement('div');
        emptyNotice.className = 'kathaingo-emoji-empty';
        emptyNotice.textContent = 'No matching emojis / பொருத்தமான இமோஜிகள் இல்லை';
        grid.appendChild(emptyNotice);
        return;
    }

    renderEmojiGrid(filtered, grid);
}

function insertEmoji(emoji) {
    if (activeEditor) {
        activeEditor.insertContent(emoji);
        activeEditor.focus();
        saveRecentEmoji(emoji);
        
        // Re-render recent category if it's currently active to update instantly
        if (currentCategory === 'Recent') {
            switchCategory('Recent');
        }
    }
}

function hidePicker() {
    if (pickerEl && pickerEl.style.display !== 'none') {
        pickerEl.style.display = 'none';
        currentTargetButton = null;
        document.removeEventListener('click', documentClickListener);
        document.removeEventListener('keydown', documentKeyDownListener);
    }
}

function documentClickListener(event) {
    if (pickerEl && !pickerEl.contains(event.target) && currentTargetButton && !currentTargetButton.contains(event.target)) {
        hidePicker();
    }
}

function documentKeyDownListener(event) {
    if (event.key === 'Escape') {
        const transDropdown = document.getElementById('lang-translit-dropdown');
        const isTransOpen = transDropdown && !transDropdown.classList.contains('hidden');
        if (isTransOpen) {
            return;
        }
        hidePicker();
    }
}

function togglePicker(button, editor) {
    if (!pickerEl) {
        createPickerDOM();
    }

    if (currentTargetButton === button) {
        hidePicker();
        return;
    }

    currentTargetButton = button;
    activeEditor = editor;

    // Reset search
    const searchInput = pickerEl.querySelector('.kathaingo-emoji-search');
    searchInput.value = '';

    // Open first view: Starter
    switchCategory('Starter');

    pickerEl.style.display = 'block';

    // Position Picker
    const rect = button.getBoundingClientRect();
    const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth;

    const pickerWidth = 280; // width of picker
    const spacing = 8;

    let topVal = rect.bottom + spacing + scrollY;
    let leftVal = rect.left + (rect.width - pickerWidth) / 2 + scrollX;

    // Viewport clamping
    if (leftVal < 8) leftVal = 8;
    if (leftVal + pickerWidth > viewportWidth - 8) {
        leftVal = viewportWidth - pickerWidth - 8;
    }

    pickerEl.style.top = `${topVal}px`;
    pickerEl.style.left = `${leftVal}px`;

    // Listeners for dismissal
    // Use timeout to prevent instant execution on the trigger click event itself
    setTimeout(() => {
        document.addEventListener('click', documentClickListener);
        document.addEventListener('keydown', documentKeyDownListener);
    }, 10);
}

window.KathaingoEmojiPicker = {
    toggle: togglePicker,
    hide: hidePicker
};
