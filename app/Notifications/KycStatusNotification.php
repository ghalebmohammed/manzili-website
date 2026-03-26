<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class KycStatusNotification extends Notification
{
    use Queueable;

    private $status;
    private $storeName;

    public function __construct($status, $storeName)
    {
        $this->status = $status;
        $this->storeName = $storeName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $title = 'تحديث حالة المتجر';
        $message = '';

        if ($this->status === 'approved') {
            $message = "تمت الموافقة على متجرك ({$this->storeName}) وهو الآن متاح للعامة.";
        }
        else if ($this->status === 'rejected') {
            $message = "عذراً، تم رفض طلب توثيق متجرك ({$this->storeName}). يرجى التأكد من البيانات.";
        }
        else {
            $message = "حالة متجرك ({$this->storeName}) تغيرت إلى: {$this->status}";
        }

        return [
            'title' => $title,
            'message' => $message,
            'url' => '/seller/dashboard'
        ];
    }
}
