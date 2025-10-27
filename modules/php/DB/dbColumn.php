<?php

namespace Bga\Games\DeadMenPax\DB;

#[\Attribute]
class dbColumn
{
    public string $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
