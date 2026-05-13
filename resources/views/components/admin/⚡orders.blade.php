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
        $query = Order::with([
            'items.variant.product'
        ])
        ->where('status', $this->activeTab);

        // NORMAL ADMINS ONLY SEE THEIR PRODUCTS' ORDERS
        if (!auth()->user()->hasRole('super admin')) {

            $userId = auth()->id();

            $query->whereHas('items.variant.product', function ($q) use ($userId) {
                $q->where('created_by', $userId);
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

    <div class="flex justify-between items-center flex-wrap gap-3">

        <div>
            <h1 class="text-2xl font-bold">
                Orders
            </h1>

            <p class="text-sm text-[#3B2F2A]/60">
                Manage customer orders and preparation progress
            </p>
        </div>

    </div>

    {{-- STATUS TABS --}}
    <div class="flex gap-2 flex-wrap">

        @foreach($statuses as $status)

            <button
                wire:click="setTab('{{ $status }}')"
                class="px-4 py-2 rounded-lg text-sm transition

                {{ $activeTab === $status
                    ? 'bg-[#3B2F2A] text-white'
                    : 'bg-white border border-[#3B2F2A]/10 text-[#3B2F2A]' }}"
            >
                {{ ucfirst($status) }}
            </button>

        @endforeach

    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden border border-[#3B2F2A]/10">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-[#E7DED5]">

                    <tr>

                        <th class="p-4 text-left">Order</th>
                        <th class="p-4 text-left">Customer</th>
                        <th class="p-4 text-left">Total</th>
                        <th class="p-4 text-left">Status</th>
                        <th class="p-4 text-left">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($orders as $order)

                        <tr class="border-t border-[#3B2F2A]/10 hover:bg-[#F6F1EB] transition">

                            <td class="p-4 font-semibold">
                                #{{ $order->id }}
                            </td>

                            <td class="p-4 text-[#3B2F2A]/80">
                                {{ $order->email }}
                            </td>

                            <td class="p-4 font-semibold text-[#38BDF8]">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>

                            <td class="p-4">

                                <span class="px-3 py-1 rounded-full text-xs bg-[#E7DED5]">
                                    {{ ucfirst($order->status) }}
                                </span>

                            </td>

                            <td class="p-4">

                                <button
                                    wire:click="selectOrder({{ $order->id }})"
                                    class="bg-[#38BDF8] hover:opacity-90 text-white px-4 py-2 rounded-lg text-xs"
                                >
                                    View
                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="p-8 text-center text-[#3B2F2A]/50">

                                No orders found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- ORDER VIEW --}}
    <livewire:admin.order-view
        :orderId="$selectedOrderId"
        :key="'order-'.$orderViewToken"
    />

</div>