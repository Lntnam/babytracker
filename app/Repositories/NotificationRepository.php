<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 22/03/2017
 * Time: 20:43
 */

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    public static function getAllUnread() {
        return Notification::where([['is_read', false]])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function closeNotification($id) {
        $n = Notification::find($id);
        if ($n) {
            $n->is_read = 1;
            $n->save();
        }
        return $n;
    }

    public static function createNotification($type, $title, $message) {
        $n = new Notification();
        $n->title = $title;
        $n->type = $type;
        $n->message = $message;
        $n->is_read = 0;

        return $n->save();
    }
}
