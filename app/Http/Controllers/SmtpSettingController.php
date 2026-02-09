<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;

class SmtpSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $smtpSettings = SmtpSetting::first();

        if (! $smtpSettings) {
            return $this->errorResponse('SMTP settings not found', 404);
        }

        return $this->successResponse($smtpSettings, 'SMTP settings retrieved successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_host' => 'required',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'nullable|in:ssl,tls',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required',
        ]);

        $smtpSettings = SmtpSetting::first();

        if (! $smtpSettings) {
            return $this->errorResponse('SMTP settings not found', 404);
        }

        $smtpSettings->mail_host = $request->mail_host;
        $smtpSettings->mail_port = $request->mail_port;
        $smtpSettings->mail_username = $request->mail_username;
        $smtpSettings->mail_password = $request->mail_password;
        $smtpSettings->mail_encryption = $request->mail_encryption;
        $smtpSettings->mail_from_address = $request->mail_from_address;
        $smtpSettings->mail_from_name = $request->mail_from_name;
        $smtpSettings->save();

        return $this->successResponse($smtpSettings, 'SMTP settings updated successfully');
    }

    public function testEmail(Request $request)
    {
        // Logic to send a test email using the current SMTP settings
    }
}
