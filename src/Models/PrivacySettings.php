<?php

namespace Happones\LaravelEvolutionClient\Models;

class PrivacySettings extends Profile
{
    /**
     * Create a new PrivacySettings instance.
     *
     * @param string $readreceipts
     * @param string $profile
     * @param string $status
     * @param string $online
     * @param string $last
     * @param string $groupadd
     */
    public function __construct(
        string $readreceipts,
        string $profile,
        string $status,
        string $online,
        string $last,
        string $groupadd
    ) {
        // Validate each parameter
        $this->validatePrivacyOption('readreceipts', $readreceipts, ['all', 'none']);
        $this->validatePrivacyOption('profile', $profile, ['all', 'contacts', 'contact_blacklist', 'none']);
        $this->validatePrivacyOption('status', $status, ['all', 'contacts', 'contact_blacklist', 'none']);
        $this->validatePrivacyOption('online', $online, ['all', 'match_last_seen']);
        $this->validatePrivacyOption('last', $last, ['all', 'contacts', 'contact_blacklist', 'none']);
        $this->validatePrivacyOption('groupadd', $groupadd, ['all', 'contacts', 'contact_blacklist']);

        parent::__construct([
            'readreceipts' => $readreceipts,
            'profile'      => $profile,
            'status'       => $status,
            'online'       => $online,
            'last'         => $last,
            'groupadd'     => $groupadd,
        ]);
    }

    /**
     * Validate a privacy option.
     *
     * @param string $option
     * @param string $value
     * @param array  $allowedValues
     *
     * @throws InvalidArgumentException
     */
    private function validatePrivacyOption(string $option, string $value, array $allowedValues): void
    {
        if (! in_array($value, $allowedValues)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value for '%s'. Allowed values: %s",
                    $option,
                    implode(', ', $allowedValues)
                )
            );
        }
    }
}
