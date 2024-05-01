<?php

namespace App\Traits;
use Exception;

trait WithOtp
{
    public function createOTP(): string 
    {
        $otp = generate6DigitOTP();
        if ($this->otp){
            $this->otp->code = $otp;
            $this->otp->save();
        } else {
            $this->otp()->create(['code' => $otp]);
        }
        return $otp;
    }

    public function verifyOTP(string $otp_code): bool 
    {
        if (!$this->otp) {
            throw new Exception('No OTP available.');
        }
        
        if ($otp_code !== $this->otp->code) {
            throw new Exception('Invalid OTP. Please try again.');
        }

        if (hasTimeExpired($this->otp->created_at)){
            throw new Exception('Expired OTP. Please request a new one.');
        }

        return true;
    }

    public function deleteOTP(): void
    {
        $this->otp()->delete();
    }
}
