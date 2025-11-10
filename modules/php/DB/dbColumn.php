<?php

namespace Bga\Games\DeadMenPax\DB;

#[\Attribute]
class dbColumn
{
    public string $name;
    
    /**
     * Constructor.
     *
     * @param string $name The name of the database column.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
