<?php
// src/Resources/Group.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class Group
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
     * Create a new Group resource instance.
     *
     * @param EvolutionService $service
     * @param string           $instanceName
     */
    public function __construct(EvolutionService $service, string $instanceName)
    {
        $this->service      = $service;
        $this->instanceName = $instanceName;
    }

    /**
     * Get the instance name.
     *
     * @return string
     */
    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    /**
     * Set the instance name.
     *
     * @param string $instanceName
     *
     * @return void
     */
    public function setInstanceName(string $instanceName): void
    {
        $this->instanceName = $instanceName;
    }

    /**
     * Get all groups.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function all(): array
    {
        return $this->service->get("/group/fetch/{$this->instanceName}");
    }

    /**
     * Get group info.
     *
     * @param string $groupId
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function find(string $groupId): array
    {
        return $this->service->get("/group/find/{$this->instanceName}", [
            'groupId' => $groupId,
        ]);
    }

    /**
     * Create a new group.
     *
     * @param string $name
     * @param array  $participants
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function create(string $name, array $participants): array
    {
        // Format participant numbers
        $formattedParticipants = array_map(function ($number) {
            return $this->formatPhoneNumber($number);
        }, $participants);

        return $this->service->post("/group/create/{$this->instanceName}", [
            'name'         => $name,
            'participants' => $formattedParticipants,
        ]);
    }

    /**
     * Format phone number to be used with the API.
     *
     * @param string $phoneNumber
     *
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters
        return preg_replace('/\D/', '', $phoneNumber);
    }

    /**
     * Update group subject.
     *
     * @param string $groupId
     * @param string $subject
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function updateSubject(string $groupId, string $subject): array
    {
        return $this->service->put("/group/update-subject/{$this->instanceName}", [
            'groupId' => $groupId,
            'subject' => $subject,
        ]);
    }

    /**
     * Update group description.
     *
     * @param string $groupId
     * @param string $description
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function updateDescription(string $groupId, string $description): array
    {
        return $this->service->put("/group/update-description/{$this->instanceName}", [
            'groupId'     => $groupId,
            'description' => $description,
        ]);
    }

    /**
     * Add participants to a group.
     *
     * @param string $groupId
     * @param array  $participants
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function addParticipants(string $groupId, array $participants): array
    {
        // Format participant numbers
        $formattedParticipants = array_map(function ($number) {
            return $this->formatPhoneNumber($number);
        }, $participants);

        return $this->service->post("/group/add-participants/{$this->instanceName}", [
            'groupId'      => $groupId,
            'participants' => $formattedParticipants,
        ]);
    }

    /**
     * Remove participants from a group.
     *
     * @param string $groupId
     * @param array  $participants
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function removeParticipants(string $groupId, array $participants): array
    {
        // Format participant numbers
        $formattedParticipants = array_map(function ($number) {
            return $this->formatPhoneNumber($number);
        }, $participants);

        return $this->service->post("/group/remove-participants/{$this->instanceName}", [
            'groupId'      => $groupId,
            'participants' => $formattedParticipants,
        ]);
    }

    /**
     * Make a participant an admin.
     *
     * @param string $groupId
     * @param string $participant
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function promoteToAdmin(string $groupId, string $participant): array
    {
        $formattedParticipant = $this->formatPhoneNumber($participant);

        return $this->service->post("/group/promote-participants/{$this->instanceName}", [
            'groupId'      => $groupId,
            'participants' => [$formattedParticipant],
        ]);
    }

    /**
     * Demote a participant from admin.
     *
     * @param string $groupId
     * @param string $participant
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function demoteFromAdmin(string $groupId, string $participant): array
    {
        $formattedParticipant = $this->formatPhoneNumber($participant);

        return $this->service->post("/group/demote-participants/{$this->instanceName}", [
            'groupId'      => $groupId,
            'participants' => [$formattedParticipant],
        ]);
    }

    /**
     * Leave a group.
     *
     * @param string $groupId
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function leave(string $groupId): array
    {
        return $this->service->post("/group/leave/{$this->instanceName}", [
            'groupId' => $groupId,
        ]);
    }

    /**
     * Get group invite code.
     *
     * @param string $groupId
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function getInviteCode(string $groupId): array
    {
        return $this->service->get("/group/invite-code/{$this->instanceName}", [
            'groupId' => $groupId,
        ]);
    }

    /**
     * Join a group using invite code.
     *
     * @param string $inviteCode
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function joinWithInviteCode(string $inviteCode): array
    {
        return $this->service->post("/group/join/{$this->instanceName}", [
            'inviteCode' => $inviteCode,
        ]);
    }
}
