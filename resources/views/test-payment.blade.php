<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Razorpay Payment Test</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<h2>Pay ₹100 for Test Plan</h2>

<button id="pay-button">Pay Now</button>

<script>
    document.getElementById('pay-button').onclick = function (e) {
        e.preventDefault();

        var options = {
            key: "rzp_test_RQAWvnhd0YNqyn", // ✅ Use your Razorpay Key ID here
            amount: 100 * 100, // amount in paise (₹100)
            currency: "INR",
            name: "Doctor SMS Plan",
            description: "Purchase 100 SMS Plan",
            handler: function (response) {
                console.log("✅ Payment Success:", response);

                if (response.razorpay_payment_id) {
                    // Redirect to your Laravel route with payment ID as query param
                    window.location.href = "/payment-success?payment_id=" + response.razorpay_payment_id;
                } else {
                    window.location.href = "/payment-failed";
                }
            },
            theme: {
                color: "#0d6efd"
            },
            modal: {
                ondismiss: function() {
                    console.log("❌ Payment popup closed by user");
                    window.location.href = "/payment-cancelled";
                }
            }
        };

        var rzp = new Razorpay(options);
        rzp.open();
    }
</script>

</body>
</html>
