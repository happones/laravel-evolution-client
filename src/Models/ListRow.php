<?php
// src/Models/ListRow.php

namespace Happones\LaravelEvolutionClient\Models;

class ListRow
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $rowId;

    /**
     * Create a new ListRow instance.
     *
     * @param string $title
     * @param string $description
     * @param string $rowId
     */
    public function __construct(string $title, string $description, string $rowId)
    {
        $this->title       = $title;
        $this->description = $description;
        $this->rowId       = $rowId;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title'       => $this->title,
            'description' => $this->description,
            'rowId'       => $this->rowId,
        ];
    }
}
