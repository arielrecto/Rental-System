<?php

    namespace App\Actions;


    class GenerateSequence
    {
        public static function generateRefNumber($prefix = 'RENT', $length = 6, $number = null)
        {
            // 0000001
            return $prefix . '-' . str_pad($number ?? rand(1, 999999), $length, '0', STR_PAD_LEFT);
        }
    }
