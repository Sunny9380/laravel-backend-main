@include('emails.components.header')

@include('emails.components.heading', ['heading' => 'Mail Verified'])

@include('emails.components.paragraph', [
    'text' =>
        'Your email has been verified successfully, You can now login to your account using your credentials',
])


@include('emails.components.paragraph', [
    'text' => 'If you did not create an account, please ignore this email',
])

@include('emails.components.footer')
