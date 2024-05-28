@include('emails.components.header')

@include('emails.components.heading', ['heading' => 'Bank Details Rejected'])

@include('emails.components.paragraph', [
    'text' => '
        Your bank details have been rejected. Please update your bank details and submit again.',
])

@include('emails.components.button', [
    'link' => env('FRONTEND_URL') . '/management/dashboard',
    'text' => 'Dashboard',
])

@include('emails.components.footer')
