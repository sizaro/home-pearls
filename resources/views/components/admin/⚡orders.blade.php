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
        $this->orderViewToken++;
    }
};
?>

<div class="max-w-6xl mx-auto p-4 space-y-4 bg-[#F6F1EB] text-[#3B2F2A] min-h-screen">

    <h1 class="text-2xl font-bold text-[#3B2F2A]">Orders</h1>

    {{-- STATUS TABS --}}
    <div class="flex gap-2 mb-4 flex-wrap">

        @foreach($statuses as $status)
            <button
                wire:click="setTab('{{ $status }}')"
                class="px-3 py-1 rounded-lg text-sm transition
                {{ $activeTab === $status 
                    ? 'bg-[#3B2F2A] text-white' 
                    : 'bg-white text-[#3B2F2A] border border-[#3B2F2A]/10' }}">
                {{ ucfirst($status) }}
            </button>
        @endforeach

    </div>

    {{-- TABLE --}}
    <div class="bg-white shadow rounded-xl overflow-hidden border border-[#3B2F2A]/10">

        <table class="w-full text-sm">

            <thead class="bg-[#E7DED5] text-[#3B2F2A]">
                <tr>
                    <th class="p-3 text-left">Order</th>
                    <th class="p-3 text-left">Customer</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($orders as $order)
                    <tr class="border-t border-[#3B2F2A]/10 hover:bg-[#F6F1EB] transition">

                        <td class="p-3 font-semibold">#{{ $order->id }}</td>

                        <td class="p-3 text-[#3B2F2A]/80">{{ $order->email }}</td>

                        <td class="p-3 text-[#38BDF8] font-semibold">
                            ${{ number_format($order->total_amount, 2) }}
                        </td>

                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs bg-[#E7DED5] text-[#3B2F2A]">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        <td class="p-3">
                            <button
                                wire:click="selectOrder({{ $order->id }})"
                                class="px-3 py-1 bg-[#38BDF8] text-white text-xs rounded-lg hover:opacity-90">
                                View
                            </button>
                        </td>

                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

    {{-- CHILD COMPONENT --}}
    <livewire:admin.order-view
        :orderId="$selectedOrderId"
        :key="'order-'.$orderViewToken"
    />

</div>