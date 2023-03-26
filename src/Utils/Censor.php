<?php

namespace App\Utils;

class Censor
{
    private $badWords = [
        'bad',
        'test',
        'hello',
        // add more bad words as needed
    ];

    public function censorString($string)
    {
        foreach ($this->badWords as $badWord) {
            $replacement = str_repeat('*', strlen($badWord));
            $string = str_ireplace($badWord, $replacement, $string);
        }

        return $string;
    }
}