<?php

namespace ADP\Helpers;

use Illuminate\Support\Facades\Mail;

class EmailHelper
{

    public static function sendMail($view, $params, $email, $razon){
        $subject = $razon;
        $for = $email;
        Mail::send($view, $params, function($msj) use($subject,$for){
            $msj->from("dtuta@engagement.com.co", "Engagement");
            $msj->subject($subject);
            $msj->to($for);
        });
    }
}
