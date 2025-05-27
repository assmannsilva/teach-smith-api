<?php

namespace App\Enums;

enum ProvidersActionsEnum: string
{
    case LOGIN = 'login';
    case REGISTER = 'register';
    case REGISTER_INVITED = 'register-invited-user';
}
