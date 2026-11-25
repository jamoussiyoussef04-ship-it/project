<script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="/assets/js/dashboard.js"></script>
<script>
document.querySelectorAll('[data-confirm]').forEach(function (button) {
    button.addEventListener('click', function (event) {
        if (!confirm(this.dataset.confirm)) event.preventDefault();
    });
});
</script>
</body>
</html>
