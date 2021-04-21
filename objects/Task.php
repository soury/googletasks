<?php

namespace soury\googletasks\objects;

class Task
{
    public function __construct($taskDetails)
    {
        foreach ($taskDetails as $key => $value) {
            $this->$key = $value;
        }
    }
}
