<?php

namespace App\Enums;

enum Bucket: string
{
    case DRIVE = 'drive';
    case PHOTOS = 'photos';
    case MUSIC = 'music';
    case AVATARS = 'avatars';
    case TEMP = 'temp';
}
