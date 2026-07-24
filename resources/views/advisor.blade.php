<x-app-layout>
    @push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="preload" href="{{ asset('video/hellow.mp4') }}" as="video" type="video/mp4">
    <link rel="preload" href="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" as="video" type="video/mp4">
    
    <style>
        :root {
            --font-premium: 'Inter', sans-serif;
            --ai-surface: #ffffff;
            --ai-bg: #f8fafc;
            --ai-border: rgba(0,0,0,0.06);
            --ai-shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --ai-shadow-lg: 0 12px 32px rgba(0,0,0,0.08);
            --ai-user-bg: linear-gradient(135deg, var(--primary) 0%, var(--accent-dim) 100%);
        }

        body {
            background-color: var(--ai-bg) !important;
            background-image: radial-gradient(circle at 50% 0%, rgba(22, 199, 183, 0.05) 0%, transparent 60%) !important;
            background-attachment: fixed !important;
        }

        .advisor-container {
            max-width: 860px;
            margin: 0 auto;
            padding: 40px 20px 140px;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 120px);
            position: relative;
            font-family: var(--font-premium);
        }

        /* ── Hero Section ── */
        .hero-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 40px;
            animation: fadeSlideUp 0.6s ease-out;
            margin-top: 40px;
        }

        .mascot-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin-bottom: 24px;
            animation: float 6s ease-in-out infinite;
        }

        .mascot-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 140%;
            height: 140%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, rgba(22, 199, 183, 0.15) 0%, rgba(22, 199, 183, 0) 65%);
            border-radius: 50%;
            z-index: 0;
            animation: breathe 4s ease-in-out infinite alternate;
        }

        .mascot-video {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: bottom;
            mix-blend-mode: darken;
            pointer-events: none;
            -webkit-mask-image: radial-gradient(circle at center 60%, black 45%, transparent 75%);
            mask-image: radial-gradient(circle at center 60%, black 45%, transparent 75%);
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.03em;
            margin: 0 0 12px;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 480px;
            margin: 0 auto;
        }

        /* ── Suggestion Cards ── */
        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            width: 100%;
            margin-top: 40px;
        }

        .suggestion-card {
            background: var(--ai-surface);
            border: 1px solid var(--ai-border);
            border-radius: 16px;
            padding: 18px;
            text-align: left;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--ai-shadow-sm);
        }

        .suggestion-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--ai-shadow-lg);
            border-color: rgba(22, 199, 183, 0.3);
        }

        .suggestion-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary-subtle);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2px;
        }

        .suggestion-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text);
            margin: 0;
        }
        
        .suggestion-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
        }

        /* ── Chat Messages ── */
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 32px;
            padding-bottom: 140px;
        }

        .message-wrapper {
            display: flex;
            gap: 16px;
            max-width: 80%;
            animation: messageEntry 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(12px);
        }

        .message-wrapper.assistant {
            align-self: flex-start;
        }

        .message-wrapper.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar img {
            width: 170%;
            height: 170%;
            object-fit: cover;
            object-position: center;
        }

        .avatar.user-avatar {
            background: var(--primary-subtle);
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
            border: 1px solid var(--ai-border);
        }

        .bubble {
            padding: 16px 20px;
            border-radius: 22px;
            font-size: 0.98rem;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .assistant .bubble {
            background: var(--ai-surface);
            border: 1px solid var(--ai-border);
            color: var(--text);
            border-top-left-radius: 4px;
            box-shadow: var(--ai-shadow-sm);
        }

        .user .bubble {
            background: var(--ai-user-bg);
            color: #ffffff;
            border-top-right-radius: 4px;
            box-shadow: 0 4px 14px rgba(22, 199, 183, 0.25);
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 16px 20px;
        }

        .typing-indicator span {
            width: 6px;
            height: 6px;
            background: var(--text-dim);
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        
        .typing-avatar-video {
            width: 160%;
            height: 160%;
            object-fit: cover;
            object-position: center;
            mix-blend-mode: darken;
        }

        /* ── Input Area ── */
        .input-wrapper {
            position: fixed;
            bottom: 32px;
            left: calc(50% + 130px); /* Offset for sidebar generally, approximate */
            transform: translateX(-50%);
            width: 100%;
            max-width: 800px;
            padding: 0 20px;
            z-index: 100;
        }

        @media (max-width: 1024px) {
            .input-wrapper { left: 50%; }
        }

        .input-container {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border: 1px solid rgba(22, 199, 183, 0.4);
            border-radius: 40px;
            padding: 8px 8px 8px 12px;
            box-shadow: 0 8px 32px rgba(22, 199, 183, 0.08);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .input-container:focus-within {
            border-color: rgba(22, 199, 183, 0.6);
            box-shadow: 0 12px 40px rgba(22, 199, 183, 0.15);
            transform: translateY(-2px);
        }

        .input-container input {
            flex: 1;
            background: rgba(22, 199, 183, 0.06);
            border: 2px solid rgba(22, 199, 183, 0.15);
            outline: none;
            color: var(--text);
            font-size: 1rem;
            padding: 12px 18px;
            border-radius: 30px;
            font-family: var(--font-premium);
            transition: all 0.2s;
        }

        .input-container input:focus {
            background: rgba(22, 199, 183, 0.1);
            border-color: rgba(22, 199, 183, 0.3);
        }

        .input-container input::placeholder {
            color: var(--text-muted);
            font-weight: 500;
        }

        .send-btn {
            background: #f1f5f9;
            color: #64748b;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            flex-shrink: 0;
        }

        .send-btn:not(:disabled) {
            background: var(--primary);
            color: #ffffff;
        }

        .send-btn:not(:disabled):hover {
            transform: scale(1.05);
            background: var(--primary-hover);
            box-shadow: 0 4px 12px rgba(22, 199, 183, 0.3);
        }
        
        .send-btn:active {
            transform: scale(0.95);
        }

        .send-btn:disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }

        /* ── Keyframes ── */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes breathe {
            0% { transform: translate(-50%, -50%) scale(0.95); opacity: 0.5; }
            100% { transform: translate(-50%, -50%) scale(1.05); opacity: 1; }
        }

        @keyframes messageEntry {
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0.6); }
            40% { transform: scale(1); }
        }
        
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endpush

    <div class="advisor-container">
        
        <!-- Hero Section (Visible only when chat is empty) -->
        <div class="hero-section" id="heroSection">
            <div class="mascot-container">
                <div class="mascot-glow"></div>
                <video autoplay loop muted playsinline class="mascot-video" id="heroMascot">
                    <source id="heroMascotSource" src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
                </video>
            </div>
            
            <h1 class="hero-title">How can I help you today?</h1>
            <p class="hero-subtitle">I'm your personal financial advisor. Ask me to create a budget, analyze your spending, or simulate financial scenarios.</p>
            
            <div class="suggestions-grid">
                <div class="suggestion-card" onclick="sendSuggestion('Set my food budget to 5000')">
                    <div class="suggestion-icon"><i data-lucide="target" style="width:20px;height:20px"></i></div>
                    <h4 class="suggestion-title">Create Budget</h4>
                    <p class="suggestion-desc">Set limits for specific categories</p>
                </div>
                <div class="suggestion-card" onclick="sendSuggestion('What is my budget status this month?')">
                    <div class="suggestion-icon"><i data-lucide="pie-chart" style="width:20px;height:20px"></i></div>
                    <h4 class="suggestion-title">Track Spending</h4>
                    <p class="suggestion-desc">Analyze your current status</p>
                </div>
                <div class="suggestion-card" onclick="sendSuggestion('What if I reduce my food expenses by 20%?')">
                    <div class="suggestion-icon"><i data-lucide="trending-up" style="width:20px;height:20px"></i></div>
                    <h4 class="suggestion-title">Simulate Savings</h4>
                    <p class="suggestion-desc">Forecast future finances</p>
                </div>
                <div class="suggestion-card" onclick="sendSuggestion('How can I save more money this month?')">
                    <div class="suggestion-icon"><i data-lucide="lightbulb" style="width:20px;height:20px"></i></div>
                    <h4 class="suggestion-title">Financial Advice</h4>
                    <p class="suggestion-desc">Get personalized tips</p>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-container" id="chatMessages" style="display: none;">
            <!-- Messages will be injected here -->
        </div>

        <!-- Floating Input -->
        <div class="input-wrapper">
            <form class="input-container" id="chatForm" autocomplete="off">
                @csrf
                <input type="text" id="messageInput" placeholder="Ask me anything about your finances..." autofocus>
                <button type="submit" id="sendBtn" class="send-btn" disabled>
                    <i data-lucide="arrow-up" style="width:20px;height:20px"></i>
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const heroSection = document.getElementById('heroSection');
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    let isFirstMessage = true;
    
    // Enable/disable send button based on input
    messageInput.addEventListener('input', function() {
        sendBtn.disabled = this.value.trim().length === 0;
    });

    function scrollToBottom() {
        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth'
        });
    }

    function addMessage(text, type) {
        if (isFirstMessage) {
            heroSection.style.display = 'none';
            chatMessages.style.display = 'flex';
            isFirstMessage = false;
        }

        const container = document.createElement('div');
        container.className = 'message-wrapper ' + type;
        
        if (type === 'assistant' || type === 'help') {
            container.innerHTML = `
                <div class="avatar">
                    <img src="{{ asset('images/logo.png') }}" alt="AI">
                </div>
                <div class="bubble">${formatText(text)}</div>
            `;
        } else {
            // User message
            container.innerHTML = `
                <div class="avatar user-avatar">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="bubble">${formatText(text)}</div>
            `;
        }
        
        chatMessages.appendChild(container);
        scrollToBottom();
    }
    
    function formatText(text) {
        // Basic formatting for line breaks and bold
        let formatted = text.replace(/\n/g, '<br>');
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        return formatted;
    }

    function showTyping() {
        if (isFirstMessage) {
            heroSection.style.display = 'none';
            chatMessages.style.display = 'flex';
            isFirstMessage = false;
        }

        const div = document.createElement('div');
        div.className = 'message-wrapper assistant';
        div.id = 'typingIndicator';
        div.style.transition = 'opacity 250ms ease';
        
        div.innerHTML = `
            <div class="avatar">
                <video autoplay loop muted playsinline class="typing-avatar-video">
                    <source src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
                </video>
            </div>
            <div class="bubble typing-indicator">
                <span></span><span></span><span></span>
            </div>
        `;
        
        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function hideTyping() {
        const el = document.getElementById('typingIndicator');
        if (el) {
            el.style.opacity = '0';
            setTimeout(() => {
                el.remove();
            }, 250);
        }
    }

    function sendSuggestion(text) {
        messageInput.value = text;
        sendBtn.disabled = false;
        chatForm.dispatchEvent(new Event('submit'));
    }

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        addMessage(message, 'user');
        messageInput.value = '';
        sendBtn.disabled = true;
        showTyping();

        try {
            const resp = await fetch('/api/advisor/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                },
                body: JSON.stringify({ question: message }),
            });

            const data = await resp.json();
            hideTyping();
            
            // Wait for typing indicator to fade out smoothly before showing the response
            await new Promise(resolve => setTimeout(resolve, 250));
            
            const reply = data.response || data.message || 'Sorry, I could not process that.';
            addMessage(reply, data.type || 'assistant');
        } catch (err) {
            hideTyping();
            await new Promise(resolve => setTimeout(resolve, 250));
            addMessage('Connection error. Please try again.', 'help');
        } finally {
            messageInput.focus();
            if (window.lucide) window.lucide.createIcons(); // refresh icons just in case
        }
    });
    </script>
    @endpush
</x-app-layout>
