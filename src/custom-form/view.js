console.log("Script is running"); // Añade esta línea al principio del archivo

(function () {
	function initForms() {
		if (typeof anjrotInviteLinks === "undefined") {
			console.error(
				"anjrotInviteLinks is not defined. Form submission may not work correctly.",
			);
			return;
		}

		document.querySelectorAll(".custom-form-container form").forEach((form) => {
			form.addEventListener("submit", function (event) {
				event.preventDefault();
				const submitAction = this.dataset.submitAction;
				const apiEndpoint = this.dataset.apiEndpoint; // Obtener el endpoint del dataset

				// Recopilar los datos del formulario
				const formData = new FormData(this);
				formData.append("action", "anjrot_submit_form");
				formData.append("_wpnonce", anjrotInviteLinks.nonce);
				formData.append("submitAction", submitAction);
				formData.append("emailTo", this.dataset.emailTo);
				formData.append("emailCc", this.dataset.emailCc);
				formData.append("emailSubject", this.dataset.emailSubject);

				// Deshabilitar el botón y mostrar efecto de carga
				const submitButton = this.querySelector('button[type="submit"]');
				submitButton.disabled = true;
				submitButton.textContent = "Enviando...";

				let endpoint = anjrotInviteLinks.ajaxUrl; // Default endpoint
				if (submitAction === "apiEndpoint") {
					endpoint = apiEndpoint; // Use custom endpoint if selected
				}

				if (
					submitAction === "sendEmail" ||
					submitAction === "sendToAPI" ||
					submitAction === "apiEndpoint"
				) {
					// Enviar los datos del formulario al servidor
					fetch(endpoint, {
						method: "POST",
						body: formData,
					})
						.then((response) => response.json())
						.then((data) => {
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
							// Rehabilitar el botón y restablecer el texto
							submitButton.disabled = false;
							submitButton.textContent = "Enviar";
						});
				} else if (submitAction === "showMessage") {
					this.innerHTML = `<div class="confirmation-message">${this.dataset.confirmationMessage}</div>`;
				} else if (submitAction === "redirect" && this.dataset.redirectUrl) {
					window.location.href = this.dataset.redirectUrl;
				} else {
					this.innerHTML = `<div class="confirmation-message">${this.dataset.confirmationMessage}</div>`;
				}
			});
		});
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initForms);
	} else {
		initForms();
	}
})();
