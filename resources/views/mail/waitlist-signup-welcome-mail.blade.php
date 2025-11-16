<?php
/**
 * @var \App\DTOs\WaitlistSignups\WaitlistSignupDTO $dto
 */
?>
<div>
    Hi {{ $dto->firstName }} {{ $dto->lastName }},
    <p>Thank you for signing up to the waitlist. We will get back to you soon.</p>
    <p>You will be notified when we launch the product.</p>
    <p>Thank you for your patience.</p>
    <p>
        Best regards,
        <br>
        {{ config('app.name') }} Team
    </p>
</div>
