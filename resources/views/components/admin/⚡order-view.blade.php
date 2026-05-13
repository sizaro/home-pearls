<?php

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

new class extends Component
{
    public $orderId = null;
    public $order = null;

    public string $adminMessage = '';

    public string $selectedStatus = '';

    public string $successMessage = '';

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

        $this->order = Order::with('items.variant.product')->find($id);

        if ($this->order) {
            $this->selectedStatus = $this->order->status;
        }

        $this->orderId = $id;
    }

    public function close()
    {
        $this->reset([
            'order',
            'orderId',
            'adminMessage',
            'selectedStatus',
            'successMessage'
        ]);
    }

    public function markReady($itemId)
    {
        $item = $this->order->items->firstWhere('id', $itemId);

        if (!$item) return;

        $isOwner = $item->variant->product->created_by === auth()->id();

        if (
            !auth()->user()->hasRole('super admin')
            && !$isOwner
        ) {
            return;
        }

        $item->ready = true;
        $item->save();

        $this->loadOrder($this->orderId);
    }

    public function updateStatus()
    {
        if (!$this->order) return;

        if (!auth()->user()->hasRole('super admin')) {
            return;
        }

        if (!$this->selectedStatus) {
            session()->flash('error', 'Please select a status first.');
            return;
        }

        if ($this->order->status === $this->selectedStatus) {
            session()->flash('error', 'Order is already in this status.');
            return;
        }

        $allReady = $this->order->items->every(fn ($i) => $i->ready);

        if (!$allReady) {
            session()->flash('error', 'All items must be marked READY first.');
            return;
        }

        $old = $this->order->status;

        $this->order->status = $this->selectedStatus;
        $this->order->save();

        $this->sendEmail($old, $this->selectedStatus);

        // refresh UI
        $this->loadOrder($this->orderId);

        // SUCCESS MESSAGE
        $this->successMessage = "Order status updated to " . ucfirst($this->selectedStatus);

        // AUTO CLEAR AFTER 4 SECONDS (Livewire-safe)
        $this->dispatch('clearSuccessMessage');
    }

    public function clearSuccessMessage()
    {
        $this->successMessage = '';
    }

    private function sendEmail($old, $new)
    {
        if (!$this->order?->email) return;

        $itemsText = $this->order->items
            ->map(fn ($i) => $i->variant->name . " (x{$i->quantity})")
            ->implode(", ");

        $message = $this->adminMessage ?: "Your order has been updated.";

        $body = "
Hello,

Your order #{$this->order->id} has been updated.

Items:
{$itemsText}

Total:
{$this->order->total_amount}

Status:
{$new}

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

@php
    $visibleItems = auth()->user()->hasRole('super admin')
        ? $order->items
        : $order->items->filter(function ($item) {
            return $item->variant->product->created_by === auth()->id();
        });

    $readyCount = $order->items->where('ready', true)->count();
    $totalCount = $order->items->count();
    $allReady = $readyCount === $totalCount;
@endphp

<div class="fixed inset-0 z-50 flex items-end md:items-center justify-center">

    <div class="absolute inset-0 bg-black/50" wire:click="close"></div>

    <div class="relative bg-[#F6F1EB] w-full md:max-w-4xl rounded-t-2xl md:rounded-xl p-5 max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center mb-5">
            <div>
                <h2 class="font-bold text-xl">
                    Order #{{ $order->id }}
                </h2>
                <p class="text-sm text-[#3B2F2A]/60">
                    {{ ucfirst($order->status) }}
                </p>
            </div>

            <button wire:click="close" class="text-xl">✕</button>
        </div>

        <div class="bg-white rounded-xl p-4 mb-5 space-y-2">
            <div><strong>Email:</strong> {{ $order->email }}</div>

            <div>
                <strong>Total:</strong>
                <span class="text-[#38BDF8] font-bold">
                    ${{ number_format($order->total_amount, 2) }}
                </span>
            </div>

            <div>
                <strong>Progress:</strong>
                {{ $readyCount }} / {{ $totalCount }} Ready
            </div>
        </div>

        {{-- ERROR --}}
        @if(session()->has('error'))
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- SUCCESS MESSAGE --}}
        @if($successMessage)
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                {{ $successMessage }}
            </div>

            <script>
                setTimeout(() => {
                    @this.call('clearSuccessMessage');
                }, 4000);
            </script>
        @endif

        {{-- STATUS CONTROL --}}
        @if(auth()->user()->hasRole('super admin'))

            <textarea
                wire:model="adminMessage"
                class="w-full border rounded-lg p-3 mb-4"
                placeholder="Optional customer message"
            ></textarea>

            <div class="flex gap-3 mb-6 items-center">

                <select
                    wire:model="selectedStatus"
                    class="w-full border rounded-lg p-2"
                >
                    <option value="">Select Status</option>

                    @foreach($statuses as $status)
                        <option value="{{ $status }}">
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach

                </select>

                <button
                    wire:click="updateStatus"
                    class="bg-[#3B2F2A] text-white px-4 py-2 rounded-lg whitespace-nowrap"
                >
                    Update
                </button>

            </div>

        @endif

        {{-- ITEMS --}}
        <div class="space-y-4">

            @foreach($visibleItems as $item)

                <div class="bg-white rounded-xl p-4 flex justify-between items-center border border-[#3B2F2A]/10">

                    <div class="flex gap-4 items-center">

                        <img
                            src="{{ route('product-variants.image', $item->variant->id) }}"
                            class="w-20 h-20 rounded-lg object-cover"
                        >

                        <div>
                            <div class="font-semibold">
                                {{ $item->variant->name }}
                            </div>

                            <div class="text-sm text-[#3B2F2A]/60">
                                Qty: {{ $item->quantity }}
                            </div>

                            <div class="text-[#38BDF8] font-bold">
                                ${{ number_format($item->variant->price, 2) }}
                            </div>
                        </div>

                    </div>

                    <div>

                        @if($item->ready)

                            <div class="text-green-600 font-bold text-sm">
                                READY
                            </div>

                        @else

                            @if(auth()->user()->hasRole('super admin') 
                                || $item->variant->product->created_by === auth()->id())

                                <button
                                    wire:click="markReady({{ $item->id }})"
                                    class="bg-[#38BDF8] text-white px-4 py-2 rounded-lg text-sm"
                                >
                                    Mark Ready
                                </button>

                            @else

                                <div class="text-gray-400 text-sm">
                                    Not yours
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