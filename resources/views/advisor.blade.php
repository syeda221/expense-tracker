<x-app-layout>
    @push('styles')
    <style>
        .advisor-container { max-width: 900px; margin: 0 auto; padding: 24px 0; display: flex; flex-direction: column; }
        .advisor-header { margin-bottom: 20px; }
        .advisor-header h1 { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0; }
        .advisor-header p { color: var(--text-dim); margin: 4px 0 0; font-size: 0.9rem; }
        .advisor-messages { min-height: 400px; max-height: 500px; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
        .advisor-messages::-webkit-scrollbar { width: 4px; }
        .advisor-messages::-webkit-scrollbar-thumb { background: var(--border-subtle); border-radius: 4px; }
        .msg { max-width: 85%; padding: 12px 16px; border-radius: 12px; line-height: 1.6; font-size: 0.9rem; animation: msgIn 0.25s ease-out; white-space: pre-wrap; word-wrap: break-word; }
        .msg.user { align-self: flex-end; background: var(--primary); color: #050809; border-bottom-right-radius: 4px; }
        .msg.assistant { align-self: flex-start; background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-subtle); border-bottom-left-radius: 4px; }
        .msg.assistant p { margin: 0; }
        .msg.help { align-self: flex-start; background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-subtle); border-left: 3px solid var(--primary); }
        @keyframes msgIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .typing-indicator { display: flex; align-items: center; gap: 4px; padding: 12px 16px; }
        .typing-indicator span { width: 6px; height: 6px; background: var(--text-dim); border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both; }
        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes bounce { 0%,80%,100% { transform: scale(0.6); } 40% { transform: scale(1); } }
        .advisor-input { margin-top: 16px; display: flex; gap: 8px; background: var(--card-bg); border: 1px solid var(--border-subtle); border-radius: 12px; padding: 4px; }
        .advisor-input:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-subtle); }
        .advisor-input input { flex: 1; background: transparent; border: none; outline: none; color: var(--text-primary); padding: 10px 12px; font-size: 0.9rem; }
        .advisor-input input::placeholder { color: var(--text-dim); }
        .advisor-input button { background: var(--primary); color: #050809; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .advisor-input button:hover { background: var(--primary-hover); }
        .advisor-input button:disabled { opacity: 0.5; cursor: not-allowed; }
        .suggestions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
        .suggestions button { background: var(--card-bg); border: 1px solid var(--border-subtle); color: var(--text-dim); padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; }
        .suggestions button:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-subtle); }
        .welcome-msg { text-align: center; padding: 40px 20px; color: var(--text-dim); }
        .welcome-msg .icon { font-size: 2.5rem; margin-bottom: 12px; }
        .welcome-msg h3 { color: var(--text-primary); margin: 0 0 8px; }
        .welcome-msg p { margin: 0; font-size: 0.9rem; }
    </style>
    @endpush

    <div class="advisor-container">
        <div class="advisor-header fade-in" style="position:relative;display:flex;flex-direction:column;align-items:center;text-align:center;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:140px 24px 40px;box-shadow:var(--shadow-card);margin-top:80px;margin-bottom:32px;overflow:visible;">
            
            <div class="owl-container" style="position:absolute;top:-80px;left:50%;transform:translateX(-50%);z-index:3;width:180px;height:220px;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;">
                <!-- Soft radial glow behind mascot -->
                <div style="position:absolute;bottom:20px;width:200px;height:200px;background:radial-gradient(circle, rgba(22,199,183,0.15) 0%, rgba(22,199,183,0) 70%);border-radius:50%;z-index:-1;"></div>
                
                <video autoplay loop muted playsinline class="owl-video" id="advisorMascot" style="width:100%;height:100%;object-fit:cover;object-position:bottom;pointer-events:auto;">
                    <source id="advisorMascotSource" src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
                </video>
            </div>

            <div style="position:relative;z-index:2">
                <h1 style="font-size:32px;font-weight:800;letter-spacing:-0.02em;color:var(--text);margin:0">Financial Advisor</h1>
                <p style="color:var(--text-muted);margin:12px auto 0;font-size:16px;line-height:1.6;max-width:400px">Ask about budgets, simulate scenarios,<br>or get personalized spending advice.</p>
            </div>
        </div>

        <div class="advisor-messages fade-in" id="chatMessages">
            <div class="msg assistant">
                👋 Hi! I'm your financial advisor. I can help you with:
                <br><br>
                <strong>📋 Budgets</strong> — "Set food budget to 5000"<br>
                <strong>📊 Status</strong> — "How much is left?" or "Budget status"<br>
                <strong>🔮 Simulations</strong> — "What if I reduce food by 20%?"<br>
                <strong>💡 Advice</strong> — "How can I save money?"
            </div>
        </div>

        <div class="suggestions" id="suggestions">
            <button onclick="sendSuggestion('Set food budget to 5000')">Set food budget</button>
            <button onclick="sendSuggestion('How much is left?')">How much left?</button>
            <button onclick="sendSuggestion('What if I reduce food by 20%?')">Simulate</button>
            <button onclick="sendSuggestion('How can I save money?')">Advice</button>
        </div>

        <form class="advisor-input" id="chatForm" autocomplete="off">
            @csrf
            <input type="text" id="messageInput" placeholder="Ask me anything about your finances..." autofocus>
            <button type="submit" id="sendBtn">
                Send
                <i data-lucide="send" style="width:14px;height:14px"></i>
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    function addMessage(text, type) {
        const div = document.createElement('div');
        div.className = 'msg ' + type;
        div.textContent = text;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTyping() {
        const div = document.createElement('div');
        div.className = 'msg assistant typing-indicator';
        div.id = 'typingIndicator';
        div.innerHTML = '<span></span><span></span><span></span>';
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        const mascot = document.getElementById('advisorMascot');
        const source = document.getElementById('advisorMascotSource');
        if (mascot && source) {
            source.src = '{{ asset("video/Owl_notices_spending_increase_202606250101.mp4") }}';
            mascot.load();
            mascot.play();
        }
    }

    function hideTyping() {
        const el = document.getElementById('typingIndicator');
        if (el) el.remove();

        const mascot = document.getElementById('advisorMascot');
        const source = document.getElementById('advisorMascotSource');
        if (mascot && source) {
            source.src = '{{ asset("video/Mascot_placing_wing_on_chin_202606242120.mp4") }}';
            mascot.load();
            mascot.play();
        }
    }

    function sendSuggestion(text) {
        messageInput.value = text;
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

            hideTyping();
            const data = await resp.json();
            const reply = data.response || data.message || 'Sorry, I could not process that.';
            addMessage(reply, data.type || 'assistant');
        } catch (err) {
            hideTyping();
            addMessage('Connection error. Please try again.', 'help');
        } finally {
            sendBtn.disabled = false;
            messageInput.focus();
        }
    });
    </script>
    @endpush
</x-app-layout>
