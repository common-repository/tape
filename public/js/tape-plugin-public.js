(function( $ ) {
	'use strict';

	$(function () {
		var isValidNumber = false,
			input = document.querySelector("#tapePhone"),
			nameField = document.querySelector("#tapeName"),
			campaignIdField = document.querySelector("#tapeCampaignId"),
			sendButton = document.querySelector("#tapeSend"),
			tapeForm = document.querySelector("#tapeForm"),
			errorMsg = document.querySelector("#tape-error-msg");
		
		const initialSendText = sendButton.value.trim();

		// here, the index maps to the error code returned from getValidationError - see readme
		var errorMap = ["Invalid number", "Invalid country code", "Number is too short", "Number is too long", "Invalid number"];

		// initialise plugin
		var iti = window.intlTelInput(input, {
			utilsScript: "utils.js",
			allowDropdown: true,
			initialCountry: "US",
			onlyCountries: ["us", "au"],
		});

		var reset = function () {
			input.classList.remove("tapeInputError");
			errorMsg.innerHTML = "";
			errorMsg.classList.add("hide");
		};

		function validate() {
			reset();
			if (input.value.trim()) {
				if (iti.isValidNumber() && !input.value.match(/[^$,.\d]/)) {
					isValidNumber = true;
				} else {
					input.classList.add("tapeInputError");
					var errorCode = iti.getValidationError();
					errorMsg.innerHTML = errorMap[errorCode];
					errorMsg.classList.remove("hide");
					isValidNumber = false;
				}
			}
		}

		function updateButtonText(textBody, type = null) {
			sendButton.value = textBody;
			sendButton.innerHTML = textBody;
			if (type === "error") {
				sendButton.classList.add("tapeSubmitError");
			} else if (type === "success") {
				sendButton.classList.add("tapeSubmitSuccess");
			}
		}

		function resetButtonText() {
			setTimeout(function () {
				sendButton.value = initialSendText;
				sendButton.innerHTML = initialSendText;
				sendButton.classList.remove("tapeSubmitError");
				sendButton.classList.remove("tapeSubmitSuccess");
			}, 1000);
		}

		function submit(e) {
			e.preventDefault();
			validate();
			if (!isValidNumber || input.value.trim().length === 0) {
				updateButtonText("Invalid Fields", "error");
				resetButtonText();
				return;
			}
			updateButtonText("Sending...");
			var jqxhrSuccess = false;
			var jqxhr = $.ajax({
				type: "POST",
				url: "https://api.trytape.com/v1.3/webhook",
				data: JSON.stringify({
					mobileNumber: iti.getNumber(intlTelInputUtils.numberFormat.E164),
					name: nameField.value.trim(),
					campaignId: campaignIdField.value.trim(),
				}),
				contentType: "application/json; charset=utf-8",
				dataType: "json",
				crossDomain: true,
				success: function (data) {
					updateButtonText("Sent Message", "success");
					input.value = "";
					nameField.value = "";
					jqxhrSuccess = true;
					resetButtonText();
				},
				fail: function (errMsg) {
					updateButtonText("Unable to Send", "error");
					jqxhrSuccess = false;
					resetButtonText();
				}
			});

			// Have to catch in case the above check fails
			jqxhr.always(function () {
				setTimeout(function () {
					if (!jqxhrSuccess) {
						updateButtonText("Unable to Send", "error");
						resetButtonText();
					}
				}, 500);
			});
		}

		// on blur: validate
		input.addEventListener('blur', validate);
		// on keyup / change flag: reset
		input.addEventListener('change', reset);
		input.addEventListener('keyup', reset);
		// on submit
		tapeForm.addEventListener("submit", submit);
	});
})( jQuery );