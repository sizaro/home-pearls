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

    // ✅ SELLER MARK READY
    public function markReady($itemId)
    {
        $item = $this->order->items->firstWhere('id', $itemId);

        if (!$item) return;

        // only owner can mark ready
        if ($item->variant->created_by !== auth()->id()) return;

        $item->ready = true;
        $item->save();

        $this->loadOrder($this->orderId);
    }

    // ✅ ADMIN UPDATE STATUS
    public function updateStatus($status)
    {
        if (!$this->order) return;

        if ($this->order->status === $status) return;

        // 🔥 ensure all items ready
        $allReady = $this->order->items->every(fn($i) => $i->ready);

        if (!$allReady) {
            session()->flash('error', 'All items must be READY first');
            return;
        }

        $old = $this->order->status;

        $this->order->status = $status;
        $this->order->save();

        $this->sendEmail($old, $status);

        $this->close();

        $this->dispatch('resetOrderView');
    }

    private function sendEmail($old, $new)
    {
        if (!$this->order?->email) return;

        $itemsText = $this->order->items->map(function ($i) {
            return $i->variant->name . " (x{$i->quantity})";
        })->implode(", ");

        $message = $this->adminMessage ?: "Your order is being processed.";

        $body = "
Hello,

Your order #{$this->order->id} has been updated.

Items:
{$itemsText}

Total: {$this->order->total_amount}

New Status: {$new}

Message:
{$message}

Thank you.
        ";

        Mail::raw($body, function ($mail) {
            $mail->to($this->order->email)
                ->subject("Order Update (#{$this->order->id})");
        });
    }
};
?>

<div>
@if($order)

<div class="fixed inset-0 z-50 flex items-end md:items-center justify-center">

    {{-- overlay --}}
    <div class="absolute inset-0 bg-black/60" wire:click="close"></div>

    {{-- modal --}}
    <div class="relative bg-white w-full md:max-w-4xl 
                rounded-t-2xl md:rounded-xl 
                p-4 max-h-[90vh] overflow-y-auto">

        {{-- mobile handle --}}
        <div class="w-12 h-1 bg-gray-300 rounded mx-auto mb-3 md:hidden"></div>

        {{-- header --}}
        <div class="flex justify-between items-center mb-3">
            <h2 class="font-bold text-lg">Order #{{ $order->id }}</h2>
            <button wire:click="close" class="text-red-500 text-xl">✕</button>
        </div>

        {{-- summary --}}
        <div class="text-sm border-b pb-3 mb-3 space-y-1">
            <div><strong>Email:</strong> {{ $order->email }}</div>
            <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
            <div><strong>Total:</strong> ${{ number_format($order->total_amount, 2) }}</div>
        </div>

        {{-- progress --}}
        @php
            $readyCount = $order->items->where('ready', true)->count();
            $totalCount = $order->items->count();
            $allReady = $readyCount === $totalCount;
        @endphp

        <div class="mb-3 text-sm">
            Progress: {{ $readyCount }} / {{ $totalCount }} ready
        </div>

        {{-- error --}}
        @if(session()->has('error'))
            <div class="text-red-600 text-sm mb-2">
                {{ session('error') }}
            </div>
        @endif

        {{-- admin message --}}
        <textarea
            wire:model="adminMessage"
            class="w-full border rounded p-2 text-sm mb-4"
            placeholder="Optional message to customer">
        </textarea>

        {{-- status buttons (ADMIN ONLY) --}}
        @if(auth()->user()->hasRole('super admin'))
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-5">
            @foreach($statuses as $status)
                @if($status !== $order->status)
                    <button
                        wire:click="updateStatus('{{ $status }}')"
                        @if(!$allReady) disabled @endif
                        class="bg-black text-white text-sm py-2 rounded disabled:opacity-40">
                        Mark {{ ucfirst($status) }}
                    </button>
                @endif
            @endforeach
        </div>
        @endif

        {{-- ITEMS --}}
        <div class="space-y-3">
            <h3 class="font-semibold">Items</h3>

            @foreach($order->items as $item)
                <div class="flex justify-between items-center border rounded-lg p-3">

                    {{-- LEFT --}}
                    <div class="flex gap-3 items-center">

                        {{-- ✅ FIXED IMAGE --}}
                        <img
                            src="{{ route('product-variants.image', $item->variant->id) }}?t={{ time() }}"
                            class="w-20 h-20 rounded object-cover"
                        >

                        <div class="text-sm">
                            <div class="font-semibold">
                                {{ $item->variant->name }}
                            </div>
                            <div>Qty: {{ $item->quantity }}</div>
                            <div class="text-gray-500">
                                ${{ number_format($item->variant->price, 2) }}
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="text-right">

                        @if($item->ready)
                            <div class="text-green-600 text-xs font-bold">
                                READY
                            </div>
                        @else
                            @if($item->variant->created_by === auth()->id())
                                <button
                                    wire:click="markReady({{ $item->id }})"
                                    class="bg-green-600 text-white text-xs px-2 py-1 rounded">
                                    Mark Ready
                                </button>
                            @else
                                <div class="text-gray-400 text-xs">
                                    Waiting
                                </div>
                            @endif
                        @endif

                    </div>

                </div>
            @endforeach

        </div>

    </div>
</div>

@endif
</div>