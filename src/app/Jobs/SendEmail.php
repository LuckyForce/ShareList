<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //Variables
    protected $recipient;
    protected $subject;
    protected $body;
    protected $altBody;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $subject, $body, $altBody)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->body = $body;
        $this->altBody = $altBody;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

            // Email server settings. Access environment variables.
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port = env('MAIL_PORT');
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($this->recipient);
            //$mail->addCC($request->emailCc);
            //$mail->addBCC($request->emailBcc);

            //$mail->addReplyTo('sender-reply-email', 'sender-reply-name');

            /*
            if(isset($_FILES['emailAttachments'])) {
                for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
                    $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
                }
            }
            */

            $mail->isHTML(true);                // Set email content format to HTML

            $mail->Subject = $this->subject;
            $mail->Body    = $this->body;
            $mail->AltBody = $this->altBody;

            if( !$mail->send() ) {
                return false;
                //return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
            }
            
            else {
                return true;
                //return back()->with("success", "Email has been sent.");
            }
        } catch (Exception $e) {
            return false;
             //return back()->with('error','Message could not be sent.');
        }
    }
}
