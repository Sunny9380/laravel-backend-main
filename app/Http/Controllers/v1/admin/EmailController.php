<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Jobs\admin\SendBulkMails;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    use HttpResponse;

    public function updateMailTemplate(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:email_templates,id',
                'subject' => 'required|string',
                'message' => 'required|string',
                'attachments' => 'nullable|array'
            ]);

            $template = EmailTemplate::find($validated['id']);
            $template->subject = $validated['subject'];
            $template->body = $validated['message'];

            //uploading attachments for future use
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('attachments');
                    $attachmentPaths[] = $path;
                }
            }


            if ($template->save()) {
                return $this->success(
                    message: 'Email template updated successfully'
                );
            } else {
                return $this->internalError(
                    message: 'Failed to update email template'
                );
            }
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function getEmailTemplate($type)
    {
        try {
            $template = EmailTemplate::where('type', $type)->first();
            return $this->success(
                data: $template
            );
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function toggleEmailStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:email_templates,id',
            ]);

            $template = EmailTemplate::find($validated['id']);
            $template->is_active = !$template->is_active;
            if ($template->save()) {
                return $this->success(
                    message: 'Email template status updated successfully'
                );
            } else {
                return $this->internalError(
                    message: 'Failed to update email template status'
                );
            }
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function getEmailTemplates()
    {
        try {
            $templates = EmailTemplate::all();
            return $this->success(
                data: $templates
            );
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    protected function sendBulkEmail($request)
    {
        try {
            $validated = $request->validate([
                'emails' => 'required|array',
                'subject' => 'required|string',
                'message' => 'required|string',
            ]);

            $emails = $request->input('emails');

            // Sending email by SendBulkEmail Job
            SendBulkMails::dispatch($emails, $request->subject, $request->message);

            return response()->json(['message' => 'Emails are being sent.'], 200);
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }


    public function sendMail(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'nullable|email',
                'subject' => 'required|string',
                'message' => 'required|string',
                'sendToAllUsers' => 'nullable|boolean',
                'sendToAllVendors' => 'nullable|boolean',
                'attachments' => 'nullable|array',
            ]);

            $emails = [];

            // if email is not null then send email to that email
            if ($validated['email']) {
                $emails[] = $validated['email'];
            }

            // if sendToAllUsers is true then send email to all users
            if ($validated['sendToAllUsers']) {
                $users = User::where('role', 0)->get();
                foreach ($users as $user) {
                    $emails[] = $user->email;
                }
            }

            // if sendToAllVendors is true then send email to all vendors
            if ($validated['sendToAllVendors']) {
                $vendors = User::where('role', 1)->get();
                foreach ($vendors as $vendor) {
                    $emails[] = $vendor->email;
                }
            }

            // Temporary store attachments
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('attachments');
                    $attachmentPaths[] = $path;
                }
            }

            // Sending email by SendBulkEmail Job
            SendBulkMails::dispatch($emails, $validated['subject'], $validated['message'], $attachmentPaths);

            return $this->success(
                message: 'Email sent successfully'
            );
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }
}
