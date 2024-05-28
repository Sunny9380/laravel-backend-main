@include('emails.components.header')

@include('emails.components.heading', ['heading' => 'Bank Details Approved'])

@include('emails.components.paragraph', [
    'text' => '
        Your bank details have been approved successfully. You can now start using our services.
    ',
])

@include('emails.components.button', [
    'link' => env('FRONTEND_URL') . '/management/dashboard',
    'text' => 'Dashboard',
])

@include('emails.components.footer')
