@component('mail::message')
# Hello {{ $invitee_name ?? 'there' }},

You've been invited to join {{ $organization_name }} **{{ config('app.name') }}**.

Click the button below to accept the invitation and complete your registration.

@component('mail::button', ['url' => $complete_registration_url])
Accept Invitation
@endcomponent

If you weren’t expecting this invitation, you can safely ignore this email.

Thanks,  
**The {{ config('app.name') }} Team**
@endcomponent
