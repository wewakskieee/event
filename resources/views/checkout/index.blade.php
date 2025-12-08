@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Confirm Your Order</h1>

            <!-- Event Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Event Details</h2>
                <div class="flex gap-4">
                    @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-24 h-24 object-cover rounded">
                    @endif
                    <div class="flex-1">
                        <h3 class="font-bold text-lg">{{ $event->title }}</h3>
                        <p class="text-gray-600 text-sm">{{ $event->location }}</p>
                        <p class="text-gray-600 text-sm">{{ $event->event_date->format('d M Y, H:i') }} WIB</p>
                        <p class="text-blue-600 font-bold mt-2">Rp {{ number_format($event->price, 0, ',', '.') }} / ticket</p>
                    </div>
                </div>
            </div>

            <!-- Form Checkout -->
            <form action="{{ route('checkout.process') }}" method="POST" x-data="checkout()" @submit="onSubmit">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="quantity" x-model="quantity">
                <input type="hidden" name="promo_code" x-model="promoCodeApplied">

                <!-- Quantity -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Quantity</h2>
                    <div class="flex items-center gap-4">
                        <button type="button" @click="decrementQty" class="w-10 h-10 bg-gray-200 rounded-lg hover:bg-gray-300 font-bold text-xl">-</button>
                        <input type="number" x-model="quantity" min="1" :max="{{ $event->quota_remaining }}" class="w-20 text-center border rounded-lg px-3 py-2 font-bold text-lg" readonly>
                        <button type="button" @click="incrementQty" class="w-10 h-10 bg-gray-200 rounded-lg hover:bg-gray-300 font-bold text-xl">+</button>
                        <span class="text-sm text-gray-600">Max: {{ $event->quota_remaining }} tickets</span>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span class="font-semibold">Rp <span x-text="formatNumber(subtotal)"></span></span>
                        </div>
                        <div class="flex justify-between" :class="discount > 0 ? 'text-red-600' : 'text-gray-400'">
                            <span>Discount:</span>
                            <span class="font-semibold">- Rp <span x-text="formatNumber(discount)"></span></span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-xl font-bold text-blue-600">
                            <span>Total Payment:</span>
                            <span>Rp <span x-text="formatNumber(total)"></span></span>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="mt-6 pt-6 border-t">
                        <label class="block text-sm font-semibold mb-2">Promo Code (optional)</label>
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                x-model="promoCode"
                                placeholder="Enter promo code"
                                class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :disabled="promoSuccess"
                            >
                            <button 
                                type="button"
                                @click="applyPromo"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold disabled:bg-gray-400"
                                :disabled="applying || promoSuccess"
                            >
                                <span x-show="!applying">Apply</span>
                                <span x-show="applying">Applying...</span>
                            </button>
                        </div>
                        <p x-show="promoMessage" class="text-sm mt-2 font-medium" :class="promoSuccess ? 'text-green-600' : 'text-red-600'">
                            <span x-text="promoMessage"></span>
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <a href="{{ route('event.detail', $event->slug) }}" class="flex-1 text-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-semibold">
                        ← Back
                    </a>
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 font-bold text-lg shadow-lg">
                        Confirm & Pay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checkout() {
    return {
        quantity: 1,
        price: {{ $event->price }},
        maxQty: {{ $event->quota_remaining }},
        subtotal: {{ $event->price }},
        discount: 0,
        total: {{ $event->price }},
        promoCode: '',
        promoCodeApplied: '',
        promoMessage: '',
        promoSuccess: false,
        applying: false,

        init() {
            this.calculateTotal();
        },

        incrementQty() {
            if (this.quantity < this.maxQty) {
                this.quantity++;
                this.calculateTotal();
            }
        },

        decrementQty() {
            if (this.quantity > 1) {
                this.quantity--;
                this.calculateTotal();
            }
        },

        calculateTotal() {
            this.subtotal = this.price * this.quantity;
            this.total = this.subtotal - this.discount;
        },

        formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        },

        async applyPromo() {
            if (!this.promoCode) {
                this.promoMessage = 'Please enter promo code';
                this.promoSuccess = false;
                return;
            }

            this.applying = true;
            this.promoMessage = '';

            try {
                const response = await fetch('{{ route('promo.validate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        promo_code: this.promoCode,
                        subtotal: this.subtotal
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.discount = data.discount_amount;
                    this.total = data.final_amount;
                    this.promoCodeApplied = this.promoCode;
                    this.promoMessage = data.message;
                    this.promoSuccess = true;
                } else {
                    this.discount = 0;
                    this.total = this.subtotal;
                    this.promoCodeApplied = '';
                    this.promoMessage = data.message;
                    this.promoSuccess = false;
                }
            } catch (e) {
                console.error(e);
                this.promoMessage = 'Failed to validate promo code';
                this.promoSuccess = false;
            } finally {
                this.applying = false;
            }
        },

        onSubmit(e) {
            // Form akan submit normal dengan data promo_code di hidden input
        }
    }
}
</script>
@endsection
