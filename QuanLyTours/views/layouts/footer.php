</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleMenu(id) {
        const el = document.getElementById(id);
        
        // Kiểm tra xem menu có tồn tại không trước khi thao tác
        if (el) {
            // Nếu đang hiện thì ẩn đi, nếu đang ẩn thì hiện lên
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