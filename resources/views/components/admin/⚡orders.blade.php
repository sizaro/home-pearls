<?php

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component
{
    public string $activeTab = 'pending'; // default tab
    public array $statuses = ['pending', 'processing', 'completed', 'delivered', 'paid'];
    public $orders = [];

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = Order::where('status', $this->activeTab)
            ->with('items.variant')
            ->latest()
            ->get();
    }

    public function setTab($status)
    {
        $this->activeTab = $status;
        $this->loadOrders();
    }

    // 🔹 Update Order Status
    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $order->status = $newStatus;
        $order->save();

        // 🔹 Send notification
        $this->notifyUser($order, $newStatus);

        $this->loadOrders();
        session()->flash('success', "Order #{$order->id} moved to {$newStatus}.");
    }

    // 🔹 Notify Customer
    protected function notifyUser($order, $status)
    {
        $message = "Your order #{$order->id} status has been updated to {$status}.";

        // Email
        if ($order->verified_contact_method === 'email' && $order->email) {
            Mail::raw($message, function($mail) use ($order) {
                $mail->to($order->email)
                     ->subject('Order Status Update');
            });
        }

        // WhatsApp placeholder
        if ($order->verified_contact_method === 'whatsapp' && $order->whatsapp) {
            // Use Twilio client once credentials available
            /*
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilio_number = env('TWILIO_WHATSAPP_NUMBER');
            $client = new Client($sid, $token);
            $client->messages->create(
                "whatsapp:".$order->whatsapp,
                ['from' => "whatsapp:$twilio_number", 'body' => $message]
            );
            */
        }
    }

}
?>

<div class="max-w-6xl mx-auto p-4">

    <h1 class="text-2xl font-bold mb-4">Orders Management</h1>

    {{-- Tabs --}}
    <div class="flex gap-4 mb-6">
        @foreach($statuses as $status)
            <button 
                wire:click="setTab('{{ $status }}')"
                class="px-4 py-2 rounded {{ $activeTab === $status ? 'bg-black text-white' : 'bg-gray-200' }}">
                {{ ucfirst($status) }}
            </button>
        @endforeach
    </div>

    {{-- Orders Table --}}
    @if($orders->isNotEmpty())
        <div class="bg-white shadow rounded p-4">
            <table class="w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-2 py-1">Order ID</th>
                        <th class="border px-2 py-1">Customer</th>
                        <th class="border px-2 py-1">Items</th>
                        <th class="border px-2 py-1">Total</th>
                        <th class="border px-2 py-1">Status</th>
                        <th class="border px-2 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="text-center border-t">
                            <td class="border px-2 py-1">{{ $order->id }}</td>
                            <td class="border px-2 py-1">
                                {{ $order->email ?? $order->whatsapp }}
                            </td>
                            <td class="border px-2 py-1">
                                @foreach($order->items as $item)
                                    {{ $item->variant->name }} (x{{ $item->quantity }})<br>
                                @endforeach
                            </td>
                            <td class="border px-2 py-1">${{ number_format($order->total_amount,2) }}</td>
                            <td class="border px-2 py-1">{{ ucfirst($order->status) }}</td>
                            <td class="border px-2 py-1 space-x-1">
                                @foreach($statuses as $status)
                                    @if($status !== $order->status)
                                        <button wire:click="updateStatus({{ $order->id }}, '{{ $status }}')"
                                            class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                                            {{ ucfirst($status) }}
                                        </button>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">No orders in this status.</p>
    @endif

    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <p class="text-green-600 mt-4">{{ session('success') }}</p>
    @endif
</div>