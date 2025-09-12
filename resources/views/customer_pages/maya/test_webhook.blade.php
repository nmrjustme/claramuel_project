<form id="webhookTestForm">
      <select name="eventType">
            <option value="PAYMENT_SUCCESS">Payment Success</option>
            <option value="PAYMENT_FAILED">Payment Failed</option>
            <option value="PAYMENT_EXPIRED">Payment Expired</option>
      </select>
      <input type="text" name="referenceNumber" placeholder="Reference Number">
      <button type="submit">Test Webhook</button>
</form>

<script>
      document.getElementById('webhookTestForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const formData = new FormData(e.target);
      const testData = {
            eventType: formData.get('eventType'),
            id: 'test-' + Date.now(),
            requestReferenceNumber: formData.get('referenceNumber') || 'ORDER',
            amount: { value: 100.00, currency: 'PHP' },
            timestamp: new Date().toISOString()
      };
      
      const response = await fetch('/maya/webhook', {
            method: 'POST',
            headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(testData)
      });
      
      console.log('Webhook test result:', await response.json());
});
</script>