<?php

namespace packages\Infrastructure\Transfer;


use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SMTPTransfer implements MailTransfer
{
    public Mailable $builder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Mailable $mailable)
    {
        $this->builder = $mailable;
    }

    public function send(SMTPSendRequest $request){
        foreach ($request->getTo() as $to){
            Mail::to($to->address)->send($this->builder);
        }

    }
}
