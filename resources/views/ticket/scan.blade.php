@extends('layouts.app')

@section('title', 'Ticket Scanner')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-center mb-8">Ticket Scanner</h1>

        <!-- Scanner using camera -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-2">Scan QR dengan Kamera</h2>
            <div id="qr-reader" style="width:100%;"></div>
            <div id="qr-reader-results" class="mt-2 font-mono text-sm text-green-700"></div>
        </div>
        
        <!-- Input fallback, tetap ada! -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Paste QR Code Hash (opsional)</label>
            <div class="flex gap-2">
                <input type="text" id="manual-input" placeholder="Paste QR code hash here..."
                       class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-600 text-lg font-mono">
                <button id="manual-validate"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-bold">
                    Validate
                </button>
            </div>
        </div>

        <!-- Hasil Validasi -->
        <div id="scan-result"></div>
    </div>
</div>

<!-- Html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
function showResult(message, success = true, ticket = null) {
    let el = document.getElementById('scan-result');
    if (!success) {
        el.innerHTML = `<div class="bg-red-100 border-2 border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">
            <b>❌ Invalid Ticket</b><br>${message}</div>`;
        return;
    }
    let html = `<div class="bg-green-100 border-2 border-green-400 text-green-800 px-6 py-4 rounded-lg mb-6">
        <b>✅ VALID TICKET</b><br>${message}`;
    if (ticket) {
        html += `<ul class="mt-2 text-left">
            <li><b>Ticket Code:</b> ${ticket.ticket_code}</li>
            <li><b>Name:</b> ${ticket.user.name}</li>
            <li><b>Email:</b> ${ticket.user.email}</li>
            <li><b>Event:</b> ${ticket.event.title}</li>
            <li><b>Status:</b> ${ticket.is_used ? '<span class="text-red-800 font-semibold">USED</span>' : '<span class="text-green-900 font-semibold">VALID</span>'}</li>
        </ul>`;
    }
    html += '</div>';
    el.innerHTML = html;
}

function doValidate(code) {
    fetch('{{ route('ticket.validate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ qr_code: code }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            showResult(data.message, true, data.ticket);
        } else {
            showResult(data.message || 'Ticket not found', false);
        }
    }).catch(() => showResult('Failed to scan/validate', false));
}

window.addEventListener('DOMContentLoaded', function() {
    if (window.Html5QrcodeScanner) {
        let qrScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250, rememberLastUsedCamera: true }
        );
        qrScanner.render(function(decodedText) {
            document.getElementById('qr-reader-results').innerText = decodedText;
            doValidate(decodedText);
            return false; // Return false to not process more
        });
    }

    // Manual input fallback
    document.getElementById('manual-validate').onclick = function() {
        let code = document.getElementById('manual-input').value.trim();
        if (code) doValidate(code);
    };
    document.getElementById('manual-input').onkeydown = function(e) {
        if (e.key === 'Enter') document.getElementById('manual-validate').click();
    };
});
</script>
@endsection
