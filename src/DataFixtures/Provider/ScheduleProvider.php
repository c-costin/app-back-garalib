<?php

namespace App\DataFixtures\Provider;

class ScheduleProvider
{
    private $days = [
        "lundi",
        "mardi",
        "mercredi",
        "jeudi",
        "vendredi",
        "samedi"
    ];

    public function getScheduleDaysLength()
    {
        return count($this->days);
    }

    public function getScheduleDays()
    {
        return $this->days;
    }
}