@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black flex flex-col">
    <div class="container mx-auto px-4 py-8 flex-1 flex flex-col">
        <h1 class="text-white text-2xl font-bold mb-4">Scan Guest QR Code</h1>
        
        <div class="flex-1 flex flex-col items-center justify-center">
            <video id="qrVideo" width="100%" class="max-w-md mb-4 border-4 border-white rounded-lg"></video>
            <div id="qrResult" class="text-white text-center mb-6"></div>
            
            <!-- Welcome message container (initially hidden) -->
            <div id="welcomeMessage" class="hidden text-center mb-6 p-4 bg-gray-800 rounded-lg max-w-md">
                <h2 class="text-xl font-bold text-green-400 mb-2" id="welcomeTitle">Welcome!</h2>
                <p class="text-white" id="customerDetails"></p>
            </div>
            
            <a href="{{ url()->previous() }}" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

<!-- CSRF token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    const video = document.getElementById("qrVideo");
    const resultContainer = document.getElementById("qrResult");
    const welcomeMessage = document.getElementById("welcomeMessage");
    const welcomeTitle = document.getElementById("welcomeTitle");
    const customerDetails = document.getElementById("customerDetails");
    let isProcessing = false;

    window.onload = function () {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(function (stream) {
                    video.srcObject = stream;
                    video.play();
                    requestAnimationFrame(scanQR);
                })
                .catch(function (err) {
                    console.error("Camera access error:", err);
                    resultContainer.innerHTML = "Could not access camera. Please grant permission.";
                });
        } else {
            resultContainer.innerHTML = "Camera not supported in this browser.";
        }
    };

    function scanQR() {
        if (isProcessing) return;

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            const canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext("2d");
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code) {
                isProcessing = true;
                resultContainer.innerHTML = "QR Code detected! Verifying...";
                processQRCode(code.data);
            } else {
                requestAnimationFrame(scanQR);
            }
        } else {
            requestAnimationFrame(scanQR);
        }
    }

    function processQRCode(qrData) {
        try {
            const data = { qr_data: qrData };
            resultContainer.innerHTML = "<div class='spinner'></div> Verifying...";
            
            fetch('/verify-qr-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // Show welcome message before redirecting
                    showWelcomeMessage(result.payment_id);
                    
                    // Stop camera
                    video.srcObject.getTracks().forEach(track => track.stop());
                    
                    // Redirect after delay
                    setTimeout(() => {
                        window.location.href = `/check-in/success/${result.payment_id}`;
                    }, 3000);
                } else {
                    resultContainer.innerHTML = "❌ Verification failed: " + result.message;
                    isProcessing = false;
                    setTimeout(() => requestAnimationFrame(scanQR), 2000);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                resultContainer.innerHTML = "❌ Error during verification. Please try again.";
                isProcessing = false;
                setTimeout(() => requestAnimationFrame(scanQR), 2000);
            });
        } catch (e) {
            console.error("Invalid QR code format:", e);
            resultContainer.innerHTML = "Invalid QR code format.";
            isProcessing = false;
            setTimeout(() => requestAnimationFrame(scanQR), 2000);
        }
    }

    function showWelcomeMessage(paymentId) {
        // Fetch customer details
        fetch(`/api/customer-details/${paymentId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const customer = data.customer;
                welcomeTitle.textContent = `Welcome, ${customer.name}!`;
                customerDetails.innerHTML = `
                    <p>Ticket: ${customer.ticket_type}</p>
                    <p>Seat: ${customer.seat_number || 'General Admission'}</p>
                    <p class="mt-2 text-green-300">Successfully checked in!</p>
                `;
                
                // Hide the QR result and show welcome message
                resultContainer.classList.add('hidden');
                welcomeMessage.classList.remove('hidden');
            } else {
                // Fallback if customer details can't be fetched
                welcomeTitle.textContent = "Welcome!";
                customerDetails.textContent = "Successfully checked in!";
                resultContainer.classList.add('hidden');
                welcomeMessage.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error("Error fetching customer details:", error);
            // Fallback if API fails
            welcomeTitle.textContent = "Welcome!";
            customerDetails.textContent = "Successfully checked in!";
            resultContainer.classList.add('hidden');
            welcomeMessage.classList.remove('hidden');
        });
    }
</script>

<style>
    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection