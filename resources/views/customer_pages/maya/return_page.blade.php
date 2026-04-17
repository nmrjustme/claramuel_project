<script>
      // Send message back to Page A
      if (window.opener) {
      window.opener.postMessage("done", "*");
      }
      // Close itself
      window.close();
</script>