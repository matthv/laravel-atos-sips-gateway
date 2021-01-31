<?php

namespace Matthv\AtosSipsGateway;

class ResponseCode
{
    public const SUCCESS = '00';

    public const REFUSED = '05';

    public const FRAUD = '34';

    public const MAX_NUMBER_OF_ATTEMPTS_REACHED = '75';

    public const TEMPORARILY_UNAVAILABLE = '90';

    public const SESSION_TIMED_OUT = '97';

    public const TECHNICAL_ERROR = '99';
}
