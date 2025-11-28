@extends('layouts.app')

@section('title', 'Checkout - ' . $event->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div x-data="{
        step: 1,
        quantity: 1,
        get total() {
            return this.quantity * {{ $event->price }};
        },
        nextStep() {
            if (this.step === 1 && this.quantity > 0) {
                this.step = 2;
            }
        },
        prevStep() {
            if (this.step > 1) {
                this.step--;
            }
        }
    }">
        <!-- Stepper -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-300 text-gray-600'" 
                         class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300">
                        1
                    </div>
                    <div class="w-32 h-1" :class="step >= 2 ? 'bg-indigo-600' : 'bg-gray-300'"></div>
                    <div :class="step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-300 text-gray-600'" 
                         class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300">
                        2
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-2 text-sm">
                <span class="w-20 text-center" :class="step === 1 ? 'text-indigo-600 font-bold' : 'text-gray-600'">
                    Select Quantity
                </span>
                <span class="w-32"></span>
                <span class="w-20 text-center" :class="step === 2 ? 'text-indigo-600 font-bold' : 'text-gray-600'">
                    Confirmation
                </span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Step 1: Select Quantity -->
            <div x-show="step === 1" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-10"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                <h2 class="text-2xl font-bold mb-6">Select Ticket Quantity</h2>
                
                <div class="mb-6">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ $event->image ? Storage::url($event->image) : 'https://via.placeholder.com/200x150' }}" 
                             alt="{{ $event->title }}" 
                             class="w-32 h-24 object-cover rounded-lg">
                        <div>
                            <h3 class="font-bold text-lg">{{ $event->title }}</h3>
                            <p class="text-gray-600">{{ $event->event_date->format('d M Y, H:i') }} WIB</p>
                            <p class="text-gray-600">{{ $event->location }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Quantity</label>
                    <div class="flex items-center gap-4">
                        <button @click="quantity = Math.max(1, quantity - 1)" 
                                class="bg-gray-200 hover:bg-gray-300 w-10 h-10 rounded-lg font-bold">
                            -
                        </button>
                        <input type="number" 
                               x-model="quantity" 
                               min="1" 
                               max="{{ $event->quota_remaining }}"
                               class="w-20 text-center border-2 border-gray-300 rounded-lg py-2 font-bold text-xl">
                        <button @click="quantity = Math.min({{ $event->quota_remaining }}, quantity + 1)" 
                                class="bg-gray-200 hover:bg-gray-300 w-10 h-10 rounded-lg font-bold">
                            +
                        </button>
                        <span class="text-gray-600">
                            (Max: {{ $event->quota_remaining }} tickets)
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Price per ticket:</span>
                        <span class="font-semibold">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Quantity:</span>
                        <span class="font-semibold" x-text="quantity"></span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span class="text-indigo-600" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <button @click="nextStep()" 
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-bold transition">
                    Continue to Confirmation →
                </button>
            </div>

            <!-- Step 2: Confirmation -->
            <div x-show="step === 2" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-10"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                <h2 class="text-2xl font-bold mb-6">Confirm Your Order</h2>

                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <input type="hidden" name="quantity" x-model="quantity">

                    <div class="mb-6">
                        <h3 class="font-bold text-lg mb-4">Order Summary</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <img src="{{ $event->image ? Storage::url($event->image) : 'https://via.placeholder.com/200x150' }}" 
                                     alt="{{ $event->title }}" 
                                     class="w-24 h-18 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-bold">{{ $event->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $event->event_date->format('d M Y, H:i') }} WIB</p>
                                    <p class="text-sm text-gray-600">{{ $event->location }}</p>
                                </div>
                            </div>
                            <div class="border-t pt-4">
                                <div class="flex justify-between mb-2">
                                    <span>Price per ticket:</span>
                                    <span>Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span>Quantity:</span>
                                    <span x-text="quantity"></span>
                                </div>
                                <div class="border-t pt-2 flex justify-between text-xl font-bold">
                                    <span>Total Payment:</span>
                                    <span class="text-indigo-600" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="font-bold text-lg mb-4">Your Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                            <p><strong>Phone:</strong> {{ auth()->user()->phone ?? 'Not set' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" 
                                @click="prevStep()" 
                                class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 font-bold transition">
                            ← Back
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-bold transition">
                            Confirm & Pay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
