<?php

// src/Resources/Message.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Models\ButtonMessage;
use Happones\LaravelEvolutionClient\Models\Contact;
use Happones\LaravelEvolutionClient\Models\ContactMessage;
use Happones\LaravelEvolutionClient\Models\ListMessage;
use Happones\LaravelEvolutionClient\Models\PollMessage;
use Happones\LaravelEvolutionClient\Models\ReactionMessage;
use Happones\LaravelEvolutionClient\Models\StatusMessage;
use Happones\LaravelEvolutionClient\Models\TemplateMessage;
use Happones\LaravelEvolutionClient\Models\TextMessage;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use InvalidArgumentException;

class Message
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
     * Create a new Message resource instance.
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

    // src/Resources/Message.php - Modify sendText method

    /**
     * Send a text message.
     *
     *
     * @throws EvolutionApiException
     * @throws InvalidArgumentException
     */
    public function sendText(
        string $phoneNumber,
        string $message,
        bool $isGroup = false,
        ?int $delay = null,
        ?bool $linkPreview = null,
        ?bool $mentionsEveryOne = null,
        ?array $mentioned = null
    ): array {
        if (empty($phoneNumber)) {
            throw new InvalidArgumentException('Phone number is required');
        }

        if (empty($message)) {
            throw new InvalidArgumentException('Message text is required');
        }

        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $textMessage = new TextMessage(
            $recipient,
            $message,
            $delay,
            null,
            $linkPreview,
            $mentionsEveryOne,
            $mentioned
        );

        return $this->service->post("/message/sendText/{$this->instanceName}", $textMessage->toArray());
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
     * Send an image message.
     *
     * Shortcut for sendMedia() with mediatype "image".
     *
     * @param string $image URL or base64 image content
     * @param string|null $mimetype MIME type; defaults to image/jpeg
     * @param string|null $fileName File name; defaults to image.jpg
     *
     * @throws EvolutionApiException
     */
    public function sendImage(
        string $phoneNumber,
        string $image,
        string $caption = '',
        bool $isGroup = false,
        ?string $mimetype = null,
        ?string $fileName = null,
        int $delay = 0
    ): array {
        return $this->sendMedia(
            $phoneNumber,
            'image',
            $mimetype ?? 'image/jpeg',
            $caption,
            $image,
            $fileName ?? 'image.jpg',
            $delay,
            $isGroup
        );
    }

    /**
     * Send a document message.
     *
     * Shortcut for sendMedia() with mediatype "document".
     *
     * @param string $document URL or base64 document content
     * @param string|null $mimetype MIME type; guessed from the file extension when null
     *
     * @throws EvolutionApiException
     */
    public function sendDocument(
        string $phoneNumber,
        string $document,
        string $fileName,
        string $caption = '',
        bool $isGroup = false,
        ?string $mimetype = null,
        int $delay = 0
    ): array {
        return $this->sendMedia(
            $phoneNumber,
            'document',
            $mimetype ?? $this->guessDocumentMimetype($fileName),
            $caption,
            $document,
            $fileName,
            $delay,
            $isGroup
        );
    }

    /**
     * Guess a document MIME type from its file extension.
     */
    protected function guessDocumentMimetype(string $fileName): string
    {
        return match (strtolower(pathinfo($fileName, PATHINFO_EXTENSION))) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            default => 'application/octet-stream',
        };
    }

    /**
     * Send a location message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendLocation(string $phoneNumber, float $latitude, float $longitude, string $name = '', string $address = '', bool $isGroup = false, int $delay = 0): array
    {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $payload = [
            'number' => $recipient,
            'name' => $name,
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if ($delay > 0) {
            $payload['delay'] = $delay;
        }

        return $this->service->post("/message/sendLocation/{$this->instanceName}", $payload);
    }

    /**
     * Send a media message (image, video or document).
     *
     * @param string $phoneNumber Recipient phone number or group id
     * @param string $mediatype Media type: image, video or document
     * @param string $mimetype MIME type, e.g. image/png
     * @param string $caption Media caption
     * @param string $media URL or base64 media content
     * @param string $fileName File name, e.g. image.png
     * @param int $delay Presence time in milliseconds before sending
     * @param bool $isGroup Whether the recipient is a group
     *
     * @throws EvolutionApiException
     */
    public function sendMedia(
        string $phoneNumber,
        string $mediatype,
        string $mimetype,
        string $caption,
        string $media,
        string $fileName,
        int $delay = 0,
        bool $isGroup = false
    ): array {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $payload = [
            'number' => $recipient,
            'mediatype' => $mediatype,
            'mimetype' => $mimetype,
            'caption' => $caption,
            'media' => $media,
            'fileName' => $fileName,
        ];

        if ($delay > 0) {
            $payload['delay'] = $delay;
        }

        return $this->service->post("/message/sendMedia/{$this->instanceName}", $payload);
    }

    /**
     * Send a contact message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendContact(string $phoneNumber, string $contactName, string $contactNumber, bool $isGroup = false): array
    {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $contact = new Contact(
            $contactName,
            $contactNumber,
            $contactNumber
        );

        $contactMessage = new ContactMessage(
            $recipient,
            [$contact]
        );

        return $this->service->post("/message/sendContact/{$this->instanceName}", $contactMessage->toArray());
    }

    /**
     * Send a poll message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendPoll(
        string $phoneNumber,
        string $name,
        int $selectableCount,
        array $values,
        ?int $delay = null,
        bool $isGroup = false
    ): array {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $pollMessage = new PollMessage(
            $recipient,
            $name,
            $selectableCount,
            $values,
            $delay
        );

        return $this->service->post("/message/sendPoll/{$this->instanceName}", $pollMessage->toArray());
    }

    /**
     * Send a list message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendList(
        string $phoneNumber,
        string $title,
        string $description,
        string $buttonText,
        string $footerText,
        array $sections,
        ?int $delay = null,
        bool $isGroup = false
    ): array {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $listMessage = new ListMessage(
            $recipient,
            $title,
            $description,
            $buttonText,
            $footerText,
            $sections,
            $delay
        );

        return $this->service->post("/message/sendList/{$this->instanceName}", $listMessage->toArray());
    }

    /**
     * Send a button message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendButtons(
        string $phoneNumber,
        string $title,
        string $description,
        string $footer,
        array $buttons,
        ?int $delay = null,
        bool $isGroup = false
    ): array {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $buttonMessage = new ButtonMessage(
            $recipient,
            $title,
            $description,
            $footer,
            $buttons,
            $delay
        );

        return $this->service->post("/message/sendButtons/{$this->instanceName}", $buttonMessage->toArray());
    }

    /**
     * Send a reaction to a message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendReaction(array $key, string $reaction): array
    {
        $reactionMessage = new ReactionMessage(
            $key,
            $reaction
        );

        return $this->service->post("/message/sendReaction/{$this->instanceName}", $reactionMessage->toArray());
    }

    /**
     * Send a status message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendStatus(
        string $type,
        string $content,
        ?string $caption = null,
        ?string $backgroundColor = null,
        ?int $font = null,
        bool $allContacts = false,
        ?array $statusJidList = null
    ): array {
        $statusMessage = new StatusMessage(
            $type,
            $content,
            $caption,
            $backgroundColor,
            $font,
            $allContacts,
            $statusJidList
        );

        return $this->service->post("/message/sendStatus/{$this->instanceName}", $statusMessage->toArray());
    }

    /**
     * Send an audio message.
     *
     * @param string $audio URL or base64
     *
     * @throws EvolutionApiException
     */
    public function sendAudio(string $phoneNumber, string $audio, bool $isGroup = false, int $delay = 1200): array
    {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        return $this->service->post("/message/sendWhatsAppAudio/{$this->instanceName}", [
            'number' => $recipient,
            'audio' => $audio,
            'delay' => $delay,
        ]);
    }

    /**
     * Send a sticker message.
     *
     * @param string $sticker URL or base64
     *
     * @throws EvolutionApiException
     */
    public function sendSticker(string $phoneNumber, string $sticker, bool $isGroup = false, int $delay = 1200): array
    {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        return $this->service->post("/message/sendSticker/{$this->instanceName}", [
            'number' => $recipient,
            'sticker' => $sticker,
            'delay' => $delay,
        ]);
    }

    /**
     * Send a template message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendTemplate(
        string $phoneNumber,
        string $name,
        string $language,
        array $components,
        ?string $webhookUrl = null,
        bool $isGroup = false
    ): array {
        $recipient = $isGroup
            ? $phoneNumber . '@g.us'
            : $this->formatPhoneNumber($phoneNumber);

        $template = new TemplateMessage(
            $recipient,
            $name,
            $language,
            $components,
            $webhookUrl
        );

        return $this->service->post("/message/sendTemplate/{$this->instanceName}", $template->toArray());
    }
}
