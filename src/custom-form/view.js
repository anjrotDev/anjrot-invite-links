console.log("Script is running");

(function () {
	function initForms() {
		console.log("initForms function called");
		if (typeof anjrotInviteLinks === "undefined") {
			console.error(
				"anjrotInviteLinks is not defined. Form submission may not work correctly.",
			);
			return;
		}

		document.querySelectorAll(".custom-form-container form").forEach((form) => {
			// Elimina posibles eventos previos antes de agregar uno nuevo
			form.removeEventListener("submit", handleFormSubmit);
			form.addEventListener("submit", handleFormSubmit);
		});
	}

	// Función de manejo del envío del formulario
	function handleFormSubmit(event) {
		event.preventDefault();
		const submitAction = this.dataset.submitAction;
		console.log("Submit action:", submitAction);

		// Deshabilitar el botón de envío para prevenir múltiples envíos
		const submitButton = this.querySelector('button[type="submit"]');
		if (submitButton) {
			submitButton.disabled = true;
		}

		// Recopilar los datos del formulario
		const formData = new FormData(this);
		formData.append("action", "anjrot_submit_form");
		formData.append("_wpnonce", anjrotInviteLinks.nonce);
		formData.append("submitAction", submitAction);
		formData.append("emailTo", this.dataset.emailTo);
		formData.append("emailCc", this.dataset.emailCc);
		formData.append("emailSubject", this.dataset.emailSubject);

		console.log("Email To:", this.dataset.emailTo);

		if (submitAction === "sendEmail" || submitAction === "sendToAPI") {
			// Enviar los datos del formulario al servidor
			fetch(anjrotInviteLinks.ajaxUrl, {
				method: "POST",
				body: formData,
			})
				.then((response) => response.json())
				.then((data) => {
					console.log("Server response:", data);
					if (data.success) {
						this.innerHTML = `<div class="confirmation-message">${this.dataset.confirmationMessage}</div>`;
					} else {
						this.innerHTML = `<div class="error-message">Error: ${data.data}</div>`;
					}
				})
				.catch((error) => {
					console.error("Error:", error);
					this.innerHTML = `<div class="error-message">An error occurred. Please try again later.</div>`;
				})
				.finally(() => {
					// Rehabilitar el botón de envío después de procesar la solicitud
					if (submitButton) {
						submitButton.disabled = false;
					}
				});
		} else if (submitAction === "showMessage") {
			this.innerHTML = `<div class="confirmation-message">${this.dataset.confirmationMessage}</div>`;
		} else if (submitAction === "redirect" && this.dataset.redirectUrl) {
			window.location.href = this.dataset.redirectUrl;
		} else {
			this.innerHTML = `<div class="confirmation-message">${this.dataset.confirmationMessage}</div>`;
		}
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initForms);
	} else {
		initForms();
	}
})();
