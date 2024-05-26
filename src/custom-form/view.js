document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll(".custom-form-container form").forEach((form) => {
		form.addEventListener("submit", function (event) {
			event.preventDefault();
			const submitAction = this.dataset.submitAction;
			const confirmationMessage = this.dataset.confirmationMessage;
			const redirectUrl = this.dataset.redirectUrl;

			if (submitAction === "redirect" && redirectUrl) {
				window.location.href = redirectUrl;
			} else {
				this.innerHTML = `<div class="confirmation-message">${confirmationMessage}</div>`;
			}
		});
	});
});
