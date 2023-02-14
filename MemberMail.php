<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class MemberMail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;
    private $member;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($member)
    {
        $this->member=$member;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $customerwelcomeTemplateId = config("welcomeTemplateId");
        $fromMail = env('MAIL_FROM_ADDRESS','');
        $FromName = env('MAIL_FROM_NAME','');

        return $this
            ->view('myTestMail')
            ->subject('Membership Email')
            ->from($fromMail, $FromName)
            ->sendgrid([
                'personalizations' => [
                    [
                       'dynamic_template_data' =>$this->member 
                    ],
                ],
            'template_id' => "d-47dbc83a15564077bafb6dcca194bf74",
        ]);
    }
}
