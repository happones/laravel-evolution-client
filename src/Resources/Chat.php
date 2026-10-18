<?php

// src/Resources/Chat.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use InvalidArgumentException;

class Chat
{
    /**
     * @var EvolutionService The Evolution service
     */
    protected EvolutionService $service;

    /**
     * @var string The instance name
     */
    protected string $instanceName;

    /**
     * Create a new Chat resource instance.
     */
    public function __construct(EvolutionService $service, string $instanceName)
    {
        $this->service = $service;
        $this->instanceName = $instanceName;
    }

    /**
     * Get the instance name.
     */
    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    /**
     * Set the instance name.
     */
    public function setInstanceName(string $instanceName): void
    {
        $this->instanceName = $instanceName;
    }

    /**
     * Get all chats.
     *
     * @throws EvolutionApiException
     */
    public function all(): array
    {
        return $this->findChats();
    }

    /**
     * Get a specific chat.
     *
     * @param string $phoneNumber Phone number with country code, or a JID
     *
     * @throws EvolutionApiException
     */
    public function find(string $phoneNumber): array
    {
        return $this->findChats([
            'where' => ['remoteJid' => $this->toJid($phoneNumber)],
        ]);
    }

    /**
     * Format a phone number for the API.
     *
     * Evolution API v2 expects the bare number with country code (it builds the
     * "@s.whatsapp.net" JID itself), so only digits are kept. Values that are
     * already a JID (e.g. "...@g.us", "...@s.whatsapp.net", "...@lid") are
     * passed through unchanged.
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        if (str_contains($phoneNumber, '@')) {
            return $phoneNumber;
        }

        return (string) preg_replace('/\D/', '', $phoneNumber);
    }

    /**
     * Get chat messages.
     *
     * @param string $phoneNumber Phone number with country code, or a JID
     * @param int $count Messages per page
     * @param int $page Page number (1-based)
     *
     * @throws EvolutionApiException
     */
    public function messages(string $phoneNumber, int $count = 20, int $page = 1): array
    {
        return $this->findMessages([
            'where' => ['key' => ['remoteJid' => $this->toJid($phoneNumber)]],
            'offset' => $count,
            'page' => $page,
        ]);
    }

    /**
     * Build a full JID from a phone number (JIDs are returned unchanged).
     *
     * Needed where Evolution filters by stored "remoteJid" instead of resolving
     * the number itself (findChats / findMessages).
     */
    protected function toJid(string $phoneNumber): string
    {
        if (str_contains($phoneNumber, '@')) {
            return $phoneNumber;
        }

        return $this->formatPhoneNumber($phoneNumber) . '@s.whatsapp.net';
    }

    /**
     * Archive a chat.
     *
     * @param string $chat Chat remote JID
     * @param array{remoteJid: string, fromMe: bool, id: string} $lastMessageKey Key of the last message in the chat
     *
     * @throws EvolutionApiException
     */
    public function archive(string $chat, array $lastMessageKey): array
    {
        return $this->setArchiveState($chat, $lastMessageKey, true);
    }

    /**
     * Unarchive a chat.
     *
     * @param string $chat Chat remote JID
     * @param array{remoteJid: string, fromMe: bool, id: string} $lastMessageKey Key of the last message in the chat
     *
     * @throws EvolutionApiException
     */
    public function unarchive(string $chat, array $lastMessageKey): array
    {
        return $this->setArchiveState($chat, $lastMessageKey, false);
    }

    /**
     * Set the archive state of a chat.
     *
     * @param string $chat Chat remote JID
     * @param array{remoteJid: string, fromMe: bool, id: string} $lastMessageKey Key of the last message in the chat
     * @param bool $archive Whether to archive the chat
     *
     * @throws EvolutionApiException
     */
    protected function setArchiveState(string $chat, array $lastMessageKey, bool $archive): array
    {
        return $this->service->post("/chat/archiveChat/{$this->instanceName}", [
            'lastMessage' => [
                'key' => $lastMessageKey,
            ],
            'archive' => $archive,
            'chat' => $chat,
        ]);
    }

    /**
     * Delete a message for everyone.
     *
     * @param string $id Message id
     * @param string $remoteJid Chat remote JID
     * @param bool $fromMe Whether the message was sent by the instance owner
     * @param string|null $participant Sender JID, for group messages only
     *
     * @throws EvolutionApiException
     */
    public function deleteMessageForEveryone(string $id, string $remoteJid, bool $fromMe, ?string $participant = null): array
    {
        $payload = [
            'id' => $id,
            'remoteJid' => $remoteJid,
            'fromMe' => $fromMe,
        ];

        if ($participant !== null) {
            $payload['participant'] = $participant;
        }

        return $this->service->delete("/chat/deleteMessageForEveryone/{$this->instanceName}", [], $payload);
    }

    /**
     * Mark messages as read.
     *
     * @param array<int, array{remoteJid: string, fromMe: bool, id: string}> $readMessages Messages to be marked as read
     *
     * @throws EvolutionApiException
     */
    public function markAsRead(array $readMessages): array
    {
        return $this->service->post("/chat/markMessageAsRead/{$this->instanceName}", [
            'readMessages' => $readMessages,
        ]);
    }

    /**
     * Mark a chat as unread.
     *
     * @param string $chat Chat remote JID
     * @param array<int, array{remoteJid: string, fromMe: bool, id: string}> $lastMessage Messages to be marked as unread
     *
     * @throws EvolutionApiException
     */
    public function markChatUnread(string $chat, array $lastMessage): array
    {
        return $this->service->post("/chat/markChatUnread/{$this->instanceName}", [
            'lastMessage' => $lastMessage,
            'chat' => $chat,
        ]);
    }

    /**
     * Start typing in a chat.
     *
     * @param string $phoneNumber Recipient phone number
     * @param int $duration Presence display time in milliseconds
     *
     * @throws EvolutionApiException
     */
    public function startTyping(string $phoneNumber, int $duration = 1000): array
    {
        return $this->sendPresence($phoneNumber, 'composing', $duration);
    }

    /**
     * Stop typing in a chat.
     *
     * @param string $phoneNumber Recipient phone number
     * @param int $duration Presence display time in milliseconds
     *
     * @throws EvolutionApiException
     */
    public function stopTyping(string $phoneNumber, int $duration = 1000): array
    {
        return $this->sendPresence($phoneNumber, 'paused', $duration);
    }

    /**
     * Send a presence update to a chat.
     *
     * @param string $phoneNumber Recipient phone number
     * @param string $presence Presence type (composing, recording, paused)
     * @param int $delay Presence display time in milliseconds
     *
     * @throws EvolutionApiException
     */
    protected function sendPresence(string $phoneNumber, string $presence, int $delay): array
    {
        $number = $this->formatPhoneNumber($phoneNumber);

        return $this->service->post("/chat/sendPresence/{$this->instanceName}", [
            'number' => $number,
            'options' => [
                'delay' => $delay,
                'presence' => $presence,
                'number' => $number,
            ],
        ]);
    }

    /**
     * Update the text content of a sent message.
     *
     * @param string $phoneNumber Recipient phone number
     * @param string $text New message content
     * @param array{remoteJid: string, fromMe: bool, id: string} $key Key of the message to update
     *
     * @throws EvolutionApiException
     */
    public function updateMessage(string $phoneNumber, string $text, array $key): array
    {
        $number = $this->formatPhoneNumber($phoneNumber);

        return $this->service->post("/chat/updateMessage/{$this->instanceName}", [
            'number' => $number,
            'text' => $text,
            'key' => $key,
        ]);
    }

    /**
     * Fetch the profile picture URL for a number.
     *
     * @param string $phoneNumber Number to fetch the profile picture URL for
     *
     * @throws EvolutionApiException
     */
    public function fetchProfilePictureUrl(string $phoneNumber): array
    {
        $number = $this->formatPhoneNumber($phoneNumber);

        return $this->service->post("/chat/fetchProfilePictureUrl/{$this->instanceName}", [
            'number' => $number,
        ]);
    }

    /**
     * Find status (stories) messages.
     *
     * @param array<string, mixed> $where Filter criteria (id, remoteJid, fromMe, ...)
     * @param int|null $limit Limit for the return
     *
     * @throws EvolutionApiException
     */
    public function findStatusMessage(array $where = [], ?int $limit = null): array
    {
        $payload = [
            'where' => $where,
        ];

        if ($limit !== null) {
            $payload['limit'] = $limit;
        }

        return $this->service->post("/chat/findStatusMessage/{$this->instanceName}", $payload);
    }

    /**
     * Get the base64 content from a media message.
     *
     * @param string $messageId Message ID
     * @param bool $convertToMp4 Whether to convert video to MP4
     *
     * @throws EvolutionApiException
     */
    public function getBase64FromMediaMessage(string $messageId, bool $convertToMp4 = false): array
    {
        return $this->service->post("/chat/getBase64FromMediaMessage/{$this->instanceName}", [
            'message' => [
                'key' => [
                    'id' => $messageId,
                ],
            ],
            'convertToMp4' => $convertToMp4,
        ]);
    }

    /**
     * Update the block status of a contact.
     *
     * @param string $phoneNumber Phone number with country code
     * @param string $status Block status: block|unblock
     *
     * @throws EvolutionApiException
     * @throws InvalidArgumentException
     */
    public function updateBlockStatus(string $phoneNumber, string $status): array
    {
        if (! in_array($status, ['block', 'unblock'], true)) {
            throw new InvalidArgumentException('Status must be one of: block, unblock');
        }

        $number = $this->formatPhoneNumber($phoneNumber);

        return $this->service->post("/message/updateBlockStatus/{$this->instanceName}", [
            'number' => $number,
            'status' => $status,
        ]);
    }

    /**
     * Search and filter chats.
     *
     * @param array $query Where/take/skip/orderBy filters
     *
     * @throws EvolutionApiException
     */
    public function findChats(array $query = []): array
    {
        return $this->service->post("/chat/findChats/{$this->instanceName}", $query);
    }

    /**
     * Search and filter messages.
     *
     * @param array $query Where/take/skip/orderBy filters
     *
     * @throws EvolutionApiException
     */
    public function findMessages(array $query = []): array
    {
        return $this->service->post("/chat/findMessages/{$this->instanceName}", $query);
    }

    /**
     * Search and filter contacts.
     *
     * @param array $query Where/take/skip/orderBy filters
     *
     * @throws EvolutionApiException
     */
    public function findContacts(array $query = []): array
    {
        return $this->service->post("/chat/findContacts/{$this->instanceName}", $query);
    }

    /**
     * Check if numbers are on WhatsApp.
     *
     * @param array $numbers Array of phone numbers to verify
     *
     * @throws EvolutionApiException
     */
    public function whatsappNumbers(array $numbers): array
    {
        return $this->service->post("/chat/whatsappNumbers/{$this->instanceName}", [
            'numbers' => $numbers,
        ]);
    }
}
