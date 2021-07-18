const styleCustom = {
    base: {
        fontSize: "16px",
        color: "#32332d",
    },
};
const card = elements.create("card", { style: styleCustom });
card.mount("#card-elements");

card.addEventListener("change", function (event) {
    var displayError = document.getElementById("card-errors");

    if (event.error) {
        displayError.textContent = event.error.message;
    } else displayError.textContent = "";
});

var form = document.getElementById("payment-form");

form.addEventListener("submit", function (event) {
    event.preventDefault();

    stripe
        .handleCardPayment(clientSecret, card, {
            payment_method_data: {
                billing_details: {
                    name: cardholderName,
                    email: cardholderEmail,
                },
            },
        })
        .then((result) => {
            if (result.error) {
                // Display error
            } else if ("paymentIntent" in result) {
                stripeTokenHandler(result.paymentIntent);
            }
        });
});

function stripeTokenHandler(intent) {
    var form = document.getElementById("payment-form");
    var inputIntentId = document.createElement("input");
    var inputIntentPaymentMethod = document.createElement("input");
    var inputIntentStatus = document.createElement("input");
    var InputSubscription = document.createElement("input");

    inputIntentId.setAttribute("type", "hidden");
    inputIntentId.setAttribute("name", "stripeIntentId");
    inputIntentId.setAttribute("value", intent.id);

    inputIntentPaymentMethod.setAttribute("type", "hidden");
    inputIntentPaymentMethod.setAttribute("name", "stripeIntentPaymentMethod");
    inputIntentPaymentMethod.setAttribute("value", intent.payment_method);

    inputIntentStatus.setAttribute("type", "hidden");
    inputIntentStatus.setAttribute("name", "stripeIntentStatus");
    inputIntentStatus.setAttribute("value", intent.status);

    InputSubscription.setAttribute("type", "hidden");
    InputSubscription.setAttribute("name", "subscription");
    InputSubscription.setAttribute("value", subscription);

    form.appendChild(inputIntentId);
    form.appendChild(inputIntentPaymentMethod);
    form.appendChild(inputIntentStatus);
    form.appendChild(InputSubscription);
    form.submit();
}
