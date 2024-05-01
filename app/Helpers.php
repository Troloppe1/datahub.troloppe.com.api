<?php

use Carbon\Carbon;

/**
 * Generates a One-Time Password (OTP).
 * 
 * This function generates a random numeric OTP of 6 digits.
 *
 * @return string The generated OTP.
 */
function generate6DigitOTP():string 
{
    return str(rand(100000, 999999))->value();
}

/**
 * Checks if the expiration timestamp has expired.
 *
 * This function calculates the expiration timestamp by adding the expHour to the createdTimestamp
 * then asserts if the expiration timestamp has expired
 *
 * @param string $createdTimestamp The creation timestamp in a valid format (e.g., 'Y-m-d H:i:s').
 * @param int $expHour The number of hours after which the time is considered expired. Defaults to 1 hour.
 * @return bool Returns true if the time has expired, otherwise false.
 */
function hasTimeExpired(string $createdTimestamp, int $expHour = 1): bool
{
    $createdTime = Carbon::parse($createdTimestamp);
    $expiredTime = $createdTime->addHours($expHour);
    $diff = $expiredTime->timestamp - Carbon::now()->timestamp;
    return $diff <= 0 ? true : false; 
}
