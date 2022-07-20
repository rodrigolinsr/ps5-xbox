<?php
namespace App\Services;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class Mailer
{
    public static function notify(string $product, string $url)
    {
        $mailTo = env('MAIL_TO_NOTIFY');

        $message = "
            <h1>Possible stock of $product available</h1>
            <p>Check the URL <a href='$url'>$url</a></p>
        ";

	Mail::html($message, function (Message $message) use ($product, $mailTo) {
            $message->subject("Possible stock of $product available");
            $mailsTo = explode(',', $mailTo);
            foreach ($mailsTo as $mailTo) {
                $message->to($mailTo);
            }
        });
    }
}
