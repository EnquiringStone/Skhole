<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 27-Jul-16
 * Time: 14:44
 */

namespace AppBundle\Util;


class RandomStringGenerator
{
    protected $alphabet;
    protected $alphabetLength;

    public function __construct($alphabet = '')
    {
        if($alphabet != '')
            $this->setAlphabet($alphabet);
        else
            $this->setAlphabet(implode(range('a', 'z')).implode(range('A', 'Z')).implode(range(0, 9)));
    }

    public function setAlphabet($alphabet)
    {
        $this->alphabet = $alphabet;
        $this->alphabetLength = strlen($alphabet);
    }

    public function generate($length)
    {
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
            $token .= $this->alphabet[$randomKey];
        }

        return $token;
    }

    protected function getRandomInteger($min, $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            // Not so random...
            return $min;
        }

        $log = log($range, 2);

        // Length in bytes.
        $bytes = (int) ($log / 8) + 1;

        // Length in bits.
        $bits = (int) $log + 1;

        // Set all lower bits to 1.
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

            // Discard irrelevant bits.
            $rnd = $rnd & $filter;

        } while ($rnd >= $range);

        return ($min + $rnd);
    }
}