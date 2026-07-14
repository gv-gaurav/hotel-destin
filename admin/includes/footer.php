    </main>

    <!-- JS Scripts -->
    <script src="../assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/vendor/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Automatically fade out and remove alerts after 3 seconds (3000ms)
            setTimeout(function() {
                $('.alert').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 3000);

            // Clean address bar query parameters to prevent alerts re-triggering on page refresh
            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                let changed = false;
                ['success', 'error', 'delete'].forEach(param => {
                    if (url.searchParams.has(param)) {
                        url.searchParams.delete(param);
                        changed = true;
                    }
                });
                if (changed) {
                    const cleanUrl = url.pathname + url.search;
                    window.history.replaceState(null, '', cleanUrl);
                }
            }
        });
    </script>
</body>
</html>
