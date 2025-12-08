@extends('layouts.app')

@section('title', 'Ticket Scanner')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg p-8" x-data="ticketScanner()" x-init="initScanner()">
        
        <h1 class="text-3xl font-bold text-center mb-8">🎟 Ticket Scanner</h1>

        <!-- Camera QR Scanner -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-2">Scan QR dengan Kamera</h2>
            <div id="qr-reader" class="rounded border p-2"></div>
            <div id="qr-reader-results" class="mt-2 font-mono text-sm text-indigo-600"></div>
        </div>

        <!-- Manual Input -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Input/Paste Ticket Code (opsional)</label>
            <div class="flex gap-2">
                <input x-model="qrCode" type="text" placeholder="Paste ticket code here..."
                       class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-600 text-lg font-mono">
                <button @click="scanTicket()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-bold">
                    Validate
                </button>
            </div>
        </div>

        <!-- Scan Result Section -->
        <div class="mt-6" x-show="result || error">
            
            <!-- SUCCESS BLOCK -->
            <template x-if="result">
                <div class="bg-green-100 border border-green-500 rounded-lg p-4">
                    <h2 class="font-bold text-green-800 text-lg">✅ VALID TICKET</h2>

                    <ul class="mt-3 text-sm text-gray-900">
                        <li><b>Ticket Code:</b> <span x-text="result.ticket_code"></span></li>
                        <li><b>Name:</b> <span x-text="result.user.name"></span></li>
                        <li><b>Email:</b> <span x-text="result.user.email"></span></li>
                        <li><b>Event:</b> <span x-text="result.event.title"></span></li>
                        <li>
                            <b>Status:</b>
                            <span class="font-bold"
                                  :class="result.is_used ? 'text-red-600' : 'text-green-600'"
                                  x-text="result.is_used ? 'USED' : 'ACTIVE'">
                            </span>
                        </li>
                    </ul>

                    <button @click="useTicket()" 
                        class="mt-4 w-full px-4 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
                        :disabled="result.is_used">
                        Mark as Used
                    </button>
                </div>
            </template>

            <!-- ERROR BLOCK -->
            <template x-if="error">
                <div class="bg-red-100 border border-red-500 text-red-700 p-4 rounded-lg font-semibold text-center">
                    ❌ <span x-text="error"></span>
                </div>
            </template>
        </div>

        <!-- RESET BUTTON -->
        <button @click="reset()" 
                class="mt-6 w-full px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            Reset
        </button>
    </div>
</div>

<!-- CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
function ticketScanner() {
    return {
        scanning: false,
        result: null,
        error: null,
        qrCode: '',
        qrScanner: null,

        initScanner() {
            if (window.Html5QrcodeScanner) {
                this.qrScanner = new Html5QrcodeScanner("qr-reader", {
                    fps: 10,
                    qrbox: 250,
                    rememberLastUsedCamera: true
                });

                this.qrScanner.render((decodedText) => {
                    this.qrCode = decodedText;
                    this.scanTicket();
                    return false;
                });
            }
        },

        async scanTicket() {
            this.error = null;
            this.result = null;

            if (!this.qrCode.trim()) {
                this.error = "⚠ Please enter or scan QR Code";
                return;
            }

            const request = await fetch(`{{ route('ticket.validate') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": `{{ csrf_token() }}`
                },
                body: JSON.stringify({ qr_code: this.qrCode })
            });

            const response = await request.json();

            if (response.valid) {
                this.result = response.ticket;
            } else {
                this.error = response.message || "Invalid Ticket";
            }
        },

        async useTicket() {
            const request = await fetch(`{{ route('ticket.use') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": `{{ csrf_token() }}`
                },
                body: JSON.stringify({ qr_code: this.qrCode })
            });

            const response = await request.json();

            if (response.valid) {
                this.result = response.ticket;
                this.error = null;
                setTimeout(() => this.reset(), 1500);
            } else {
                this.error = response.message;
            }
        },

        reset() {
            this.qrCode = '';
            this.result = null;
            this.error = null;
        }
    }
}
</script>
@endsection
