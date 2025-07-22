<?php
// src/Models/Label.php

namespace Happones\LaravelEvolutionClient\Models;

use InvalidArgumentException;

class Label
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $labelId;

    /**
     * @var string
     */
    protected $action;

    /**
     * Create a new Label instance.
     *
     * @param string $number  The phone number
     * @param string $labelId The label ID
     * @param string $action  The action (add or remove)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $number, string $labelId, string $action)
    {
        if (! in_array($action, ['add', 'remove'])) {
            throw new InvalidArgumentException("Action must be 'add' or 'remove'");
        }

        $this->number  = $number;
        $this->labelId = $labelId;
        $this->action  = $action;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'number'  => $this->number,
            'labelId' => $this->labelId,
            'action'  => $this->action,
        ];
    }
}
