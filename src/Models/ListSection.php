<?php
// src/Models/ListSection.php

namespace Happones\LaravelEvolutionClient\Models;

class ListSection
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * Create a new ListSection instance.
     *
     * @param string $title
     * @param array  $rows
     */
    public function __construct(string $title, array $rows)
    {
        $this->title = $title;

        $rowsArray = [];

        foreach ($rows as $row) {
            if ($row instanceof ListRow) {
                $rowsArray[] = $row->toArray();
            } else {
                $rowsArray[] = $row;
            }
        }

        $this->rows = $rowsArray;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'rows'  => $this->rows,
        ];
    }
}
