<!-- Custom-Switcher JS -->
<script src="{{ asset("/backend/js/custom-switcher.min.js") }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset("/backend/libs/bootstrap/js/bootstrap.bundle.min.js") }}"></script>

<!-- Show Password JS -->
<script src="{{ asset("/backend/js/show-password.js") }}"></script>

<!-- Internal Two Step Verification JS -->
<script src="{{ asset("/backend/js/two-step-verification.js") }}"></script>

<script>
    setInterval(() => {
    fetch('/refresh-csrf')
        .then(response => response.json())
        .then(data => {
            document.querySelector('input[name="_token"]').value = data.csrf_token;
        });
}, 600000);
</script>

</body>

</html>
