<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("label").forEach(label => {
            label.addEventListener("click", function () {
                const input = document.getElementById(this.getAttribute("for"));
                if (input) {
                    input.focus();
                }
            });
        });
    });
</script>   
