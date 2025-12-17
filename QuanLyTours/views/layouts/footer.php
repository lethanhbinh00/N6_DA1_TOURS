</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function toggleMenu(id) {
        const el = document.getElementById(id);
        
        // Kiểm tra xem menu có tồn tại không trước khi thao tác
        if (el) {
            // Đảm bảo thao tác với style.display
            if (el.style.display === 'block') {
                el.style.display = 'none';
            } else {
                el.style.display = 'block';
            }
        } else {
            console.error("Không tìm thấy menu với ID: " + id);
        }
    }
</script>

</body>
</html>