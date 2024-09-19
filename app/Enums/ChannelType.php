<?php

namespace App\Enums;

enum ChannelType: string
{
    case Group = 'group';
    case Event = 'event';
    case Profile = 'profile';
}
