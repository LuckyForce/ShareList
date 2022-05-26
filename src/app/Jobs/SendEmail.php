<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        //Make API Request to https://www.adrian-schauer.at/projects/emailapi
        //Make API request so that the program does not wait for the api request to answer.
        //Needed Post Fields:
        //$_POST['sender']
        //$_POST['email']
        //$_POST['recipient']
        //$_POST['subject']
        //$_POST['html-message']
        //$_POST['text-message']
        //$_POST['service']
        //$_POST['key']

        // https://stackoverflow.com/questions/8024821/php-curl-required-only-to-send-and-not-wait-for-response
        // https://stackoverflow.com/questions/1918383/dont-echo-out-curl

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.adrian-schauer.at/projects/emailapi/src/index.php');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'sender' => env('APP_NAME'),
            'email' => $this->recipient,
            'recipient' => $this->recipient,
            'subject' => $this->subject,
            'html-message' => $this->body,
            'text-message' => $this->altBody,
            'service' => env('EMAIL_API_SERVICE'),
            'key' => env('EMAIL_API_KEY')
        ));
        curl_setopt($ch, CURLOPT_USERAGENT, 'api');

        curl_setopt($ch, CURLOPT_TIMEOUT, 1); //if your connect is longer than 1s it lose data in POST better is finish script in recieve
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //Do not echo out the result
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);

        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        
        curl_exec($ch);
        curl_close($ch);
        //print_r(curl_getinfo($ch));
    }
}
