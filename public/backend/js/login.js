$("#adminLoginForm").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this); // Create FormData object

    // Append CSRF token manually to FormData
    formData.append('_token', $('meta[name="csrf-token"]').attr("content"));

    $.ajax({
        url: "/login-submit", // Your actual login route
        type: "POST",
        data: formData,
        cache: false,
        contentType: false, // Required for FormData
        processData: false, // Required for FormData
        beforeSend: function () {
            $("#adminBtn").html('<i class="bx bx-radio-circle bx-burst bx-md"></i>'); // Indicate processing
        },
        success: function (response) {
            if (response.success) {
                $("#adminBtn").html("Redirecting...");
                setTimeout(function(){
                    window.location.href="/dashboard"
                },3500);
            } else {
                $("#adminBtn").html("Login");
                // Show error message
                showToast('error',response.message);
            }
        },
        error: function (xhr) {
            // This will capture any server errors (500, etc.)
            const response = xhr.responseJSON;
            if (response && response.message) {
                alert(response.message); // Show error message from server
            } else {
                alert('An unexpected error occurred. Please try again.'); // Generic error message
            }
        }
    });
});

function showToast(type, message) {
    $.toast({
        text: message,
        heading: type === "success" ? "Success" : "Error",
        icon: type,
        showHideTransition: "fade",
        allowToastClose: true,
        hideAfter: 1700,
        position: "mid-center",
        textAlign: "center",
        loaderBg: type === "success" ? "#9EC600" : "#FF5C5C",
    });
}