<?php

namespace App\Enums;

enum ChatEventEnum : string
{
    case MESSAGE_CREATED = 'message_created';
    case MESSAGE_UPDATED = 'message_updated';
    case MESSAGE_DELETED = 'message_deleted';
    case NEW_UPDATED = 'new_update';
    case NEW_CHAPTER = 'new_chapter';
    case NEXT_LIVE_SETUP = 'next_live_setup';
}
