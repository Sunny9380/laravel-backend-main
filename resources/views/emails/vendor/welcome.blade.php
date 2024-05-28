@include('emails.components.header')

@include('emails.components.heading', ['heading' => 'Vendor Account Created Successfully'])

@include('emails.components.paragraph', [
    'text' => '
        Your account has been created successfully. You can now login to your account and start using our services.
    ',
])

@include('emails.components.subheading', ['heading' => 'Your Account Details'])

@include('emails.components.list', [
    'list' => [
        'Vendor ID: ' . $vendor_id,
        'Email: ' . $email,
        'Password: ' . $password,
    ],
])

@include('emails.components.button', [
    'link' => env('FRONTEND_URL') . '/management/dashboard',
    'text' => 'Dashboard',
])

@include('emails.components.footer')
