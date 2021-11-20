<?php

namespace packages\Infrastructure\Transfer;

interface MailTransfer
{
    public function send(SMTPSendRequest $request);
}
