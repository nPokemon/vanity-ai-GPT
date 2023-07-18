<?php

namespace App\Enums;

enum Queue: string
{
    case NOTIFICATIONS = 'notifications';

    public static function all(): array
    {
        $items = [];

        foreach (self::cases() as $item) {
            $items[] = $item->value;
        }

        return $items;
    }
}
