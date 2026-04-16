<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    /**
     * When an order is created
     */
    public function orderPlaced(Order $order)
    {
        $this->sendEmail(
            $order->email,
            'Order Confirmation',
            $this->orderPlacedMessage($order)
        );
    }

    /**
     * When order status changes (processing, shipped, completed)
     */
    public function orderStatusUpdated(Order $order)
    {
        $this->sendEmail(
            $order->email,
            'Order Status Updated',
            $this->orderStatusMessage($order)
        );
    }

    /**
     * Core email sender (centralized)
     */
    private function sendEmail($to, $subject, $body)
    {
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject);
        });
    }

    /**
     * Message templates
     */
    private function orderPlacedMessage(Order $order)
    {
        return "Your order #{$order->id} has been successfully placed.\n\nWe will start processing it soon.";
    }

    private function orderStatusMessage(Order $order)
    {
        return "Your order #{$order->id} status has been updated to: {$order->status}.";
    }
}