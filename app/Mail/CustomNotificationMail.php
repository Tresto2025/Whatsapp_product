<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $firstname;
    public $lastname;
    public $introLines;
    public $actionText;
    public $actionUrl;
    public $outroLines;
    public $salutation;

    public function __construct($data = [])
    {
        $this->title = $data['title'] ?? '';
        $this->firstname = $data['firstname'] ?? '';
        $this->lastname = $data['lastname'] ?? '';
        $this->introLines = $data['introLines'] ?? [];
        $this->actionText = $data['actionText'] ?? null;
        $this->actionUrl = $data['actionUrl'] ?? null;
        $this->outroLines = $data['outroLines'] ?? [];
        $this->salutation = $data['salutation'] ?? null;
    }

    public function build()
    {
        return $this->view('vendor.notifications.email') // your email Blade template
                    ->with([
                        'logo' => $this->embed(public_path('uploads/logo/logo.png')),
                        'title' => $this->title,
                        'firstname' => $this->firstname,
                        'lastname' => $this->lastname,
                        'introLines' => $this->introLines,
                        'actionText' => $this->actionText,
                        'actionUrl' => $this->actionUrl,
                        'outroLines' => $this->outroLines,
                        'salutation' => $this->salutation,
                    ]);
    }
}
