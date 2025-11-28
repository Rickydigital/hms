<!-- resources/views/components/chat.blade.php -->

<!-- Chat CSS -->
<style>
    /* Chat styles with Blue Bahari theme */
    .chat-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #0077B6; /* Blue Bahari */
        color: white;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        cursor: pointer;
        animation: bounce 2s infinite;
    }
    .chat-button:hover {
        background: #0096C7; /* Brighter Blue Bahari */
        transform: scale(1.1);
    }
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    .chat-window {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 350px;
        max-height: 500px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        z-index: 1000;
        border: 1px solid #b6d4fe; /* UNESCO border */
    }
    .chat-header {
        background: #0077B6; /* Blue Bahari */
        color: white;
        padding: 12px;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-header h4 {
        margin: 0;
        font-size: 16px;
        color: #FFFFFF; /* Pure white */
    }
    .chat-header .btn-secondary {
        background: #0096C7; /* Brighter Blue Bahari */
        border: none;
    }
    .chat-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 15px;
        max-height: 360px;
        background: #F1FAFF; /* Very light blue */
    }
    .message {
        display: flex;
        margin-bottom: 12px;
        font-size: 14px;
    }
    .sent {
        justify-content: flex-end;
    }
    .received {
        justify-content: flex-start;
    }
    .message-bubble {
        max-width: 70%;
        padding: 8px 12px;
        border-radius: 12px;
        position: relative;
    }
    .sent .message-bubble {
        background: #CAF0F8; /* Pale blue, like #e3f2fd */
        color: #333;
        border-bottom-right-radius: 4px;
    }
    .received .message-bubble {
        background: #F1FAFF; /* Very light blue, like #e9f2ff */
        color: #333;
        border-bottom-left-radius: 4px;
    }
    .message-bubble p {
        margin: 0 0 4px 0;
    }
    .message-bubble small {
        font-size: 10px;
        color: #666;
        display: block;
        text-align: right;
    }
    .welcome-message {
        text-align: center;
        background: #90E0EF; /* Aqua, vibrant */
        color: #333;
        font-style: italic;
        padding: 10px;
        border-radius: 12px;
        margin: 0 auto;
        font-size: 14px;
    }
    .chat-footer {
        padding: 10px;
        border-top: 1px solid #b6d4fe; /* UNESCO border */
        background: #fff;
    }
    .btn-unesco {
        background: #0077B6; /* Blue Bahari */
        color: white;
        border: none;
    }
    .btn-unesco:hover {
        background: #0096C7; /* Brighter Blue Bahari */
    }
    .unread-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #0077B6; /* Blue Bahari */
        border-radius: 50%;
        margin-right: 5px;
        align-self: flex-start;
        margin-top: 8px;
    }
    .loader-dots {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
    }
    .loader-dots span {
        display: inline-block;
        width: 10px;
        height: 10px;
        background: #0077B6; /* Blue Bahari */
        border-radius: 50%;
        margin: 0 4px;
        animation: pulse 1s infinite ease-in-out;
    }
    .loader-dots span:nth-child(2) {
        animation-delay: 0.3s;
    }
    .loader-dots span:nth-child(3) {
        animation-delay: 0.6s;
    }
    @keyframes pulse {
        0% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(0.8); opacity: 0.5; }
    }
    .pending-message .message-bubble {
        background: #90E0EF; /* Aqua for pending */
        opacity: 0.7;
    }
</style>

<!-- Chat HTML -->
@if (Auth::check())
    <!-- Floating Chat Button -->
    <button id="chat-toggle" class="chat-button">
        <i class="fas fa-comments"></i>
    </button>

    <!-- Chat Window (Hidden by Default) -->
    <div id="chat-window" class="chat-window d-none">
        <div class="chat-header">
            <h4>{{ __('messages.group_chat') }} ({{ Auth::user()->getRoleNames()->first() }})</h4>
            <button id="chat-close" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></button>
        </div>
        <div class="chat-body">
            <div id="chat-loader" class="text-center d-none">
                <div class="loader-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div id="chat-messages"></div>
        </div>
        <div class="chat-footer">
            <small class="text-muted d-block mb-2">{{ __('messages.messages_shared_with_role') }}</small>
            <select id="institution-id" class="form-control mb-2">
                <option value="">{{ __('messages.no_institution_tag') }}</option>
                @foreach ($institutions ?? \App\Models\Institution::select('institution_id', 'name')->get() as $institution)
                    <option value="{{ $institution->institution_id }}"
                        {{ Auth::user()->institution_id == $institution->institution_id ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            <div class="input-group">
                <input id="chat-input" class="form-control" placeholder="{{ __('messages.type_message') }}" />
                <button id="chat-send" class="btn btn-unesco"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
@endif

<!-- Chat JavaScript -->
@if (Auth::check())
    <script>
        // Clear any residual Mockjax handlers
        if (typeof $.mockjax !== 'undefined') {
            $.mockjaxClear();
        }

        $(document).ready(function () {
            let lastMessageId = 0;
            let isChatOpen = false;
            const currentUserId = {{ Auth::user()->user_id }};
            const userRole = '{{ Auth::user()->getRoleNames()->first() }}';
            let isSending = false;
            let pendingMessages = new Map();

            // Toggle chat window
            $('#chat-toggle').on('click', function (e) {
                e.stopPropagation(); // Prevent triggering document click
                $('#chat-window').toggleClass('d-none');
                isChatOpen = !isChatOpen;
                if (isChatOpen) {
                    $('#chat-messages').html(`
                        <div class="message welcome-message">
                            <p>{{ str_replace(':role', Auth::user()->getRoleNames()->first(), __('messages.welcome_message')) }}</p>
                        </div>
                    `);
                    fetchInitialMessages();
                    markMessagesAsRead();
                    scrollToBottom();
                }
            });

            // Close chat window
            $('#chat-close').on('click', function (e) {
                e.stopPropagation();
                $('#chat-window').addClass('d-none');
                isChatOpen = false;
            });

            // Close chat on outside click
            $(document).on('click', function (e) {
                if (isChatOpen && !$(e.target).closest('#chat-window').length && !$(e.target).closest('#chat-toggle').length) {
                    $('#chat-window').addClass('d-none');
                    isChatOpen = false;
                }
            });

            // Prevent closing when clicking inside chat window
            $('#chat-window').on('click', function (e) {
                e.stopPropagation();
            });

            // Send message with debounce
            $('#chat-send').on('click', debounce(sendMessage, 500));
            $('#chat-input').on('keypress', function (e) {
                if (e.which === 13 && !e.shiftKey) {
                    debounce(sendMessage, 500)();
                }
            });

            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction() {
                    const later = () => {
                        clearTimeout(timeout);
                        func();
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            function sendMessage() {
                if (isSending) return;
                const message = $('#chat-input').val().trim();
                if (!message) return;

                isSending = true;
                const localId = 'local-' + Date.now();
                const institutionName = $('#institution-id option:selected').text();
                const createdAt = new Date().toLocaleString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).replace(',', '');

                // Clear input immediately and show pending message
                $('#chat-input').val('');
                $('#chat-messages').append(`
                    <div class="message sent pending-message" data-local-id="${localId}">
                        <div class="message-bubble">
                            <p><strong>${$('<div/>').text('{{ Auth::user()->name }}').html()}</strong> ${institutionName && institutionName !== '{{ __('messages.no_institution_tag') }}' ? `(${$('<div/>').text(institutionName).html()})` : ''}</p>
                            <p>${$('<div/>').text(message).html()}</p>
                            <small>${$('<div/>').text(createdAt).html()}</small>
                        </div>
                    </div>
                `);
                scrollToBottom();
                pendingMessages.set(localId, message);

                // Debug AJAX timing
                console.time('sendMessage');
                $.ajax({
                    url: '/chat/send',
                    method: 'POST',
                    data: {
                        message: message,
                        institution_id: $('#institution-id').val() || null,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('#chat-loader').removeClass('d-none');
                    },
                    success: function (response) {
                        if (response.status === 'Message sent') {
                            $(`[data-local-id="${localId}"]`).removeClass('pending-message').attr('data-message-id', response.message.id);
                            pendingMessages.delete(localId);
                            lastMessageId = Math.max(lastMessageId, response.message.id);
                            $('#institution-id').val({{ Auth::user()->institution_id ?? "''" }});
                            scrollToBottom();
                        }
                    },
                    error: function (xhr) {
                        $(`[data-local-id="${localId}"]`).remove();
                        pendingMessages.delete(localId);
                        Swal.fire('Error', 'Failed to send message: ' + (xhr.responseJSON?.message || 'Server error'), 'error');
                    },
                    complete: function () {
                        $('#chat-loader').addClass('d-none');
                        isSending = false;
                        console.timeEnd('sendMessage');
                    }
                });
            }

            function fetchInitialMessages() {
                $.ajax({
                    url: '/chat/messages',
                    method: 'GET',
                    data: { last_id: 0 },
                    beforeSend: function () {
                        $('#chat-loader').removeClass('d-none');
                    },
                    success: function (messages) {
                        $('#chat-messages').empty();
                        if (messages.length > 0) {
                            messages.forEach(function (message) {
                                const unreadDot = (!message.read && message.user_id !== currentUserId) ? '<span class="unread-dot"></span>' : '';
                                $('#chat-messages').append(`
                                    <div class="message ${message.user_id === currentUserId ? 'sent' : 'received'}" data-message-id="${message.id}">
                                        ${unreadDot}
                                        <div class="message-bubble">
                                            <p><strong>${$('<div/>').text(message.user).html()}</strong> ${message.institution ? `(${$('<div/>').text(message.institution).html()})` : ''}</p>
                                            <p>${$('<div/>').text(message.message).html()}</p>
                                            <small>${$('<div/>').text(message.created_at).html()}</small>
                                        </div>
                                    </div>
                                `);
                                lastMessageId = Math.max(lastMessageId, message.id);
                            });
                        } else {
                            $('#chat-messages').html(`
                                <div class="message welcome-message">
                                    <p>Welcome to the ${$('<div/>').text(userRole).html()} group chat! Connect with your team.</p>
                                </div>
                            `);
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Failed to fetch messages: ' + (xhr.responseJSON?.message || 'Server error'), 'error');
                    },
                    complete: function () {
                        $('#chat-loader').addClass('d-none');
                        if (isChatOpen) scrollToBottom();
                    }
                });
            }

            function fetchMessages() {
                $.ajax({
                    url: '/chat/messages',
                    method: 'GET',
                    data: { last_id: lastMessageId },
                    success: function (messages) {
                        if (messages.length > 0) {
                            messages.forEach(function (message) {
                                const unreadDot = (!message.read && message.user_id !== currentUserId) ? '<span class="unread-dot"></span>' : '';
                                $('#chat-messages').append(`
                                    <div class="message ${message.user_id === currentUserId ? 'sent' : 'received'}" data-message-id="${message.id}">
                                        ${unreadDot}
                                        <div class="message-bubble">
                                            <p><strong>${$('<div/>').text(message.user).html()}</strong> ${message.institution ? `(${$('<div/>').text(message.institution).html()})` : ''}</p>
                                            <p>${$('<div/>').text(message.message).html()}</p>
                                            <small>${$('<div/>').text(message.created_at).html()}</small>
                                        </div>
                                    </div>
                                `);
                                lastMessageId = Math.max(lastMessageId, message.id);
                            });
                            if (isChatOpen) {
                                markMessagesAsRead();
                                scrollToBottom();
                            }
                        }
                    },
                    error: function (xhr) {
                        console.error('Error fetching messages:', xhr.responseText);
                    },
                    complete: function () {
                        setTimeout(fetchMessages, 5000);
                    }
                });
            }

            function markMessagesAsRead() {
                $('#chat-messages .message.received').each(function () {
                    const messageId = $(this).data('message-id');
                    if ($(this).find('.unread-dot').length) {
                        $.ajax({
                            url: '/chat/mark-read',
                            method: 'POST',
                            data: {
                                message_id: messageId,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function () {
                                $(`[data-message-id="${messageId}"] .unread-dot`).remove();
                            },
                            error: function (xhr) {
                                console.error('Error marking message as read:', xhr.responseText);
                            }
                        });
                    }
                });
            }

            function scrollToBottom() {
                $('#chat-messages').animate({ scrollTop: $('#chat-messages')[0].scrollHeight }, 300);
            }

            // Start polling
            fetchMessages();
        });
    </script>
@endif