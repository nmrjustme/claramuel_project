@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black flex flex-col">
      <div class="container mx-auto px-4 py-8 flex-1 flex flex-col">
            <h1 class="text-white text-2xl font-bold mb-4">Scan Guest QR Code</h1>
            
            <div class="flex-1 flex flex-col items-center justify-center">
                  <video id="qrVideo" width="100%" class="max-w-md mb-4 border-4 border-white rounded-lg"></video>
                  <div id="qrResult" class="text-white text-center mb-6"></div>
                  
                  <!-- Welcome message container (initially hidden) -->
                  <div id="goodbyeMessage" class="hidden text-center mb-6 p-4 bg-gray-800 rounded-lg max-w-md">
                        <h2 class="text-xl font-bold text-green-400 mb-2" id="goodbyeTitle">Thank you!</h2>
                        <p class="text-white" id="customerDetails"></p>
                  </div>
                  
                  <a href="{{ url()->previous() }}"
                        class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
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
      const goodbyeMessage = document.getElementById("goodbyeMessage");
      const goodbyeTitle = document.getElementById("goodbyeTitle");
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
      
      async function processQRCode(qrData) {
            try {
                  console.log("📦 QR Data:", qrData);
                  
                  // Validate input
                  if (!qrData || typeof qrData !== 'string') {
                  throw new Error("Invalid QR code data");
                  }
      
                  resultContainer.innerHTML = "<div class='spinner'></div> Verifying...";
                  
                  // Create the request payload
                  const payload = {
                  qr_data: qrData
                  };
      
                  const response = await fetch('/verify-qr-codes/checkout', {
                  method: 'POST',
                  headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                  },
                  body: JSON.stringify(payload)
                  });
                  
                  if (response.status === 409) {
                        const conflictData = await response.json();
                        // Optional: Show a message first before redirecting
                        resultContainer.innerHTML = `⚠️ ${conflictData.message || "QR Code already used."}`;
                        return;
                  }
                  // Redirect to a "conflict" page or show QR image if needed
                  // setTimeout(() => {
                  //       window.location.href = `/check-in/used?path=${encodeURIComponent(conflictData.qr_path)}`;
                  // }, 2000);
                  // return;
                  // }
                  
                  // Handle response
                  if (!response.ok) {
                  const errorData = await response.json().catch(() => null);
                  throw new Error(errorData?.message || `Server error: ${response.status}`);
                  }
      
                  const result = await response.json();
                  
                  if (!result.success) {
                  throw new Error(result.message || "Verification failed");
                  }
                  
                  // Success case
                  showThankyouMessage(result.payment_id);
                  video.srcObject.getTracks().forEach(track => track.stop());
                  
                  setTimeout(() => {
                  window.location.href = `/check-out/receipt/${result.payment_id}`;
                  }, 3000);
      
            } catch (error) {
                  console.error("Verification error:", error);
                  resultContainer.innerHTML = `❌ ${error.message || "An error occurred"}`;
                  isProcessing = false;
                  setTimeout(() => requestAnimationFrame(scanQR), 2000);
            }
      }

      function showThankyouMessage(paymentId) {
            fetch(`/qrScanner/customer-details/${paymentId}`, {
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
                  goodbyeTitle.textContent = `Thank you, ${customer.name}!`;                
                  resultContainer.classList.add('hidden');
                  goodbyeMessage.classList.remove('hidden');
                  } else {
                  goodbyeTitle.textContent = "Thank you!";
                  customerDetails.textContent = "Successfully checked in!";
                  resultContainer.classList.add('hidden');
                  goodbyeMessage.classList.remove('hidden');
                  }
            })
            .catch(error => {
                  console.error("Error fetching customer details:", error);
                  goodbyeTitle.textContent = "Thank you!";
                  customerDetails.textContent = "Successfully checked in!";
                  resultContainer.classList.add('hidden');
                  goodbyeMessage.classList.remove('hidden');
            });
      }
</script>

<style>
      .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
      }

      @keyframes spin {
            to {
                  transform: rotate(360deg);
            }
      }
</style>
@endsection