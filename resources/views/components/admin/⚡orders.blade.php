<?php

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component
{
    public string $activeTab = 'pending';

    public $selectedOrderId = null;
    public int $orderViewToken = 0;

    public array $statuses = [
        'pending',
        'processing',
        'completed',
        'delivered',
        'paid'
    ];

    public $orders = [];

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $query = Order::with('items.variant')
            ->where('status', $this->activeTab);

        if (!auth()->user()->hasRole('super admin')) {
            $query->whereHas('items.variant', function ($q) {
                $q->where('created_by', auth()->id());
            });
        }

        $this->orders = $query->latest()->get();
    }

    public function setTab($status)
    {
        $this->activeTab = $status;
        $this->selectedOrderId = null;
        $this->loadOrders();
    }

    public function selectOrder($id)
    {
        $this->selectedOrderId = $id;

        // 🔥 KEEP YOUR WORKING REMOUNT SYSTEM
        $this->orderViewToken++;
    }
};
?>

<div class="max-w-6xl mx-auto p-4 space-y-4">

    <h1 class="text-2xl font-bold">Orders</h1>

    {{-- STATUS TABS --}}
    <div class="flex gap-2 mb-4">
        @foreach($statuses as $status)
            <button
                wire:click="setTab('{{ $status }}')"
                class="px-3 py-1 rounded text-sm
                {{ $activeTab === $status ? 'bg-black text-white' : 'bg-gray-200' }}">
                {{ ucfirst($status) }}
            </button>
        @endforeach
    </div>

    {{-- TABLE --}}
    <table class="w-full text-sm bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">Order</th>
                <th class="p-2 text-left">Customer</th>
                <th class="p-2 text-left">Total</th>
                <th class="p-2 text-left">Status</th>
                <th class="p-2 text-left">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($orders as $order)
                <tr class="border-t">
                    <td class="p-2">#{{ $order->id }}</td>
                    <td class="p-2">{{ $order->email }}</td>
                    <td class="p-2">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="p-2">{{ ucfirst($order->status) }}</td>
                    <td class="p-2">
                        <button
                            wire:click="selectOrder({{ $order->id }})"
                            class="px-3 py-1 bg-red-600 text-white text-xs rounded">
                            View
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 🔥 CHILD COMPONENT (YOUR TOKEN SYSTEM KEPT) --}}
    <livewire:admin.order-view
        :orderId="$selectedOrderId"
        :key="'order-'.$orderViewToken"
    />

</div>