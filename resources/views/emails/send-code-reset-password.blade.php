@include('emails.components.header')

@include('emails.components.heading', ['heading' => 'Reset Password'])

@include('emails.components.paragraph', [
    'text' => 'We have received your request to reset your account password, You can use the following code to recover your account:',
])


@include('emails.components.otp', ['otp' => $code])

@include('emails.components.paragraph', [
    'text' => 'The allowed duration of the code is one hour from the time the message was sent, If you did not request a password reset, please ignore this email',
])

@include('emails.components.footer')
