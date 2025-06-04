<script>
    @if (session('success'))
        toastr.success("{{ session('success') }}", "Success");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}", "Error");
    @endif

    @if (session('info'))
        toastr.info("{{ session('info') }}", "Info");
    @endif

    @if (session('warning'))
        toastr.warning("{{ session('warning') }}", "Warning");
    @endif

    @if ($errors->any())
        toastr.error(
            `{!! implode('<br>', $errors->all()) !!}`,
            "Validation Error"
        );
    @endif
</script>
