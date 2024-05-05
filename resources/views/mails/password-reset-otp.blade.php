<x-data-hub-mail-layout :$title :$username>

    <p>Your One-Time Password (OTP) for changing your password:</p>
    <h2 class="primary-color"> {{ $otp }} </h2>
    <p>This OTP is valid for a single login attempt and will expire after an hour.</p>

</x-data-hub-mail-layout>