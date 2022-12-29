<?php

namespace Enum;

enum PasswordResetStatus
{
    case VALID;
    case INVALID;
    case EXPIRED;
    case UNKNOWN;
}