<?php

namespace App\DataFixtures\Provider;

class PhoneProvider
{
    private $indicative = [1,2,3,4,5,6,7,9];

    public function getPhoneNumber()
    {
        $indicative = array_rand($this->indicative);

        $phoneNumber = "0" . $indicative . mt_rand(0,9).mt_rand(0,9) . mt_rand(0,9).mt_rand(0,9) . mt_rand(0,9).mt_rand(0,9) . mt_rand(0,9).mt_rand(0,9);

        return $phoneNumber;
    }
}