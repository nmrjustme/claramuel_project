<script>
      if (window.opener) {
            // Focus the parent tab
            window.opener.focus();
            
            // Optionally reload or redirect parent tab
            window.opener.location.href = '/booking/received';
            
            // Close the checkout tab
            window.close();
      }
</script>