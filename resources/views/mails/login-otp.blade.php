<x-data-hub-mail-layout :$title :$username>

    <p>Your One-Time Password (OTP) for logging into your dashboard is:</p>
    <h2 class="primary-color"> {{ $otp }} </h2>
    <p>This OTP is valid for a single login attempt and will expire after an hour.</p>

</x-data-hub-mail-layout>