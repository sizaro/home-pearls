<?php

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

new class extends Component
{
    public $orderId = null;
    public $order = null;

    public string $adminMessage = '';

    public array $statuses = [
        'pending',
        'processing',
        'completed',
        'delivered',
        'paid'
    ];

    public function mount($orderId = null)
    {
        $this->loadOrder($orderId);
    }

    public function updatedOrderId($value)
    {
        $this->loadOrder($value);
    }

    private function loadOrder($id)
    {
        if (!$id) return;

        $this->order = Order::with('items.variant')->find($id);
        $this->orderId = $id;
    }

    public function close()
    {
        $this->reset(['order', 'orderId', 'adminMessage']);
    }

    public function updateStatus($status)
    {
        if (!$this->order) return;

        $old = $this->order->status;

        $this->order->status = $status;
        $this->order->save();

        $this->sendEmail($old, $status);
    }

    private function sendEmail($old, $new)
    {
        if (!$this->order?->email) return;

        $message = $this->adminMessage ?: "Your order #{$this->order->id} has been updated.";

        Mail::raw(
            "Order #{$this->order->id}\n\nStatus: {$old} → {$new}\n\nMessage:\n{$message}",
            function ($mail) {
                $mail->to($this->order->email)
                    ->subject("Order Update");
            }
        );
    }
};
?>

<div>
    
@if($order)
<div class="fixed inset-0 z-50 flex items-end md:items-center justify-center">

    {{-- overlay --}}
    <div class="absolute inset-0 bg-black/60" wire:click="close"></div>

    {{-- sheet --}}
    <div class="relative bg-white w-full md:max-w-2xl rounded-t-2xl md:rounded-xl p-4 max-h-[85vh] overflow-y-auto">

        {{-- drag handle --}}
        <div class="w-12 h-1 bg-gray-300 rounded mx-auto mb-3 md:hidden"></div>

        {{-- header --}}
        <div class="flex justify-between items-center mb-3">
            <h2 class="font-bold">Order #{{ $order->id }}</h2>

            <button wire:click="close" class="text-red-500 text-lg">✕</button>
        </div>

        {{-- info --}}
        <div class="text-sm border-b pb-3 mb-3">
            <div><strong>Email:</strong> {{ $order->email }}</div>
            <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
            <div><strong>Total:</strong> ${{ number_format($order->total_amount, 2) }}</div>
        </div>

        {{-- ADMIN MESSAGE --}}
        <textarea
            wire:model="adminMessage"
            class="w-full border rounded p-2 text-sm mb-3"
            placeholder="Write message to customer (optional)">
        </textarea>

        {{-- STATUS ACTIONS --}}
        <div class="grid grid-cols-2 gap-2 mb-4">
            @foreach($statuses as $status)
                @if($status !== $order->status)
                    <button
                        wire:click="updateStatus('{{ $status }}')"
                        class="bg-blue-600 text-white text-xs py-2 rounded">
                        Mark {{ ucfirst($status) }}
                    </button>
                @endif
            @endforeach
        </div>

        {{-- ITEMS --}}
        @foreach($order->items as $item)
            <div class="flex gap-2 border p-2 rounded mb-2">
                <img src="{{ asset('storage/'.$item->variant->image) }}"
                     class="w-10 h-10 rounded">

                <div class="text-xs">
                    <div class="font-semibold">{{ $item->variant->name }}</div>
                    <div>Qty: {{ $item->quantity }}</div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endif
</div>