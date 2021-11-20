<?php

namespace packages\Domain\Basic\Mail;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Validation\RFCValidation;

class Address
{
    private Mail $mail;
    public string $personal;
    public string $address;

    public function __construct(Mail $mail, string $personal)
    {
        $this->mail = $mail;
        $this->personal = $personal;
        $this->toInternetAddress();
    }

    public function toInternetAddress() {
        $valid = new RFCValidation();
        try{
            $valid->isValid($this->mail->toString(),new EmailLexer());
        }catch (\Exception $e){
            //$valid->getError();
            //$valid->getWarnings();
        }
        $this->address = $this->mail->toString();
    }


}