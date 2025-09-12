<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <script src="https://cdn.tailwindcss.com"></script>
      <title>Document</title>
</head>

<body>
      <div class="container py-5">
            <div class="row justify-content-center">
                  <div class="col-md-8">
                        <div class="card shadow-sm">
                              <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Checkout with Maya</h4>
                              </div>
                              <div class="card-body">
                                    <div class="row">
                                          <div class="col-md-6">
                                                <div class="border p-4 rounded mb-4">
                                                      <h5 class="mb-3">Order Summary</h5>
                                                      @php

                                                      $cartTotal = 100;
                                                      $shippingFee = 50;
                                                      $tax = 10;
                                                      $totalAmount = ($cartTotal + $shippingFee + $tax);

                                                      @endphp

                                                      <div class="d-flex justify-content-between mb-2">
                                                            <span>Subtotal:</span>
                                                            <span>₱{{ number_format($cartTotal, 2) }}</span>
                                                      </div>

                                                      <div class="d-flex justify-content-between mb-2">
                                                            <span>Shipping:</span>
                                                            <span>₱{{ number_format($shippingFee, 2) }}</span>
                                                      </div>

                                                      <div class="d-flex justify-content-between mb-2">
                                                            <span>Tax:</span>
                                                            <span>₱{{ number_format($tax, 2) }}</span>
                                                      </div>

                                                      <hr>

                                                      <div class="d-flex justify-content-between fw-bold">
                                                            <span>Total Amount:</span>
                                                            <span>₱{{ number_format($totalAmount, 2) }}</span>
                                                      </div>
                                                </div>
                                          </div>

                                          <div class="col-md-6">
                                                <div class="border p-4 rounded">
                                                      <h5 class="mb-3">Payment Method</h5>

                                                      <div class="text-center mb-4">
                                                            <img src="{{ asset('images/maya-logo.png') }}" alt="Maya"
                                                                  style="height: 50px;">
                                                            <p class="text-muted small mt-2">Secure payment powered by
                                                                  Maya
                                                            </p>
                                                      </div>

                                                      <form id="paymentForm" action="{{ route('maya.create.session') }}"
                                                            method="POST">
                                                            @csrf
                                                            
                                                            <input type="hidden" name="totalAmount"
                                                                  value="{{ $totalAmount }}">

                                                            <div class="mb-3">
                                                                  <label class="form-label">Email Address</label>
                                                                  <input type="email" class="form-control" name="email"
                                                                        value="richmayandoc11@gmail.com" required
                                                                        readonly>
                                                            </div>

                                                            <div class="form-check mb-3">
                                                                  <input class="form-check-input" type="checkbox"
                                                                        id="termsCheck" required>
                                                                  <label class="form-check-label" for="termsCheck">
                                                                        I agree to the <a href="#">Terms of Service</a>
                                                                        and
                                                                        <a href="#">Privacy Policy</a>
                                                                  </label>
                                                            </div>

                                                            <button type="submit" class="btn btn-primary w-100 py-3">
                                                                  <i class="fas fa-lock me-2"></i>Pay ₱{{
                                                                  number_format($totalAmount, 2) }}
                                                            </button>
                                                      </form>

                                                      <div class="mt-3 text-center">
                                                            <img src="{{ asset('images/secure-payment.png') }}"
                                                                  alt="Secure Payment" style="height: 30px;">
                                                            <p class="small text-muted mt-2">
                                                                  Your payment details are encrypted and secure
                                                            </p>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
      <script>
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            
            this.submit();
      });
      </script>

      <style>
            .card {
                  border: none;
                  border-radius: 15px;
            }

            .card-header {
                  border-radius: 15px 15px 0 0 !important;
            }

            .border {
                  border-radius: 10px;
            }

            .btn {
                  border-radius: 8px;
                  font-weight: 600;
            }
      </style>
</body>

</html>