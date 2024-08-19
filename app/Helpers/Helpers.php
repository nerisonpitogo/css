<?php

if (!function_exists('toCents')) {
    /**
     * Convert an amount in pesos to cents.
     *
     * @param float $amount The amount in pesos.
     * @return int The amount in cents.
     */
    function toCents($amount)
    {
        return (int) round($amount * 100);
    }
}

if (!function_exists('toPesos')) {
    /**
     * Convert an amount in cents to pesos.
     *
     * @param int $amount The amount in cents.
     * @return float The amount in pesos.
     */
    function toPesos($amount)
    {
        return $amount / 100;
    }
}
