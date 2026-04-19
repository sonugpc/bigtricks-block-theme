/**
 * Bigtricks Contact Forms Handler
 * Handles both Contact and Advertise forms submission
 */

document.addEventListener("DOMContentLoaded", function () {
  // Handle all Bigtricks forms
  const forms = document.querySelectorAll(
    ".bt-contact-form-element, .bt-advertise-form-element",
  );

  forms.forEach(function (form) {
    // Cache DOM elements for performance
    const submitButton = form.querySelector('button[type="submit"]');
    const submitText = submitButton.querySelector(".submit-text");
    const messagesContainer = form.querySelector(".form-messages");
    const originalText = submitText.textContent;

    // Store cached elements on form
    form._cached = {
      submitButton,
      submitText,
      messagesContainer,
      originalText,
    };

    form.addEventListener("submit", handleFormSubmit);
  });

  function showError(form, message) {
    const { messagesContainer } = form._cached;
    messagesContainer.classList.remove("hidden");
    messagesContainer.innerHTML = `
			<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-start gap-3">
				<i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
				<div>
					<p class="font-semibold">Validation Error</p>
					<p class="text-sm mt-1">${message}</p>
				</div>
			</div>
		`;
    if (typeof lucide !== "undefined") {
      lucide.createIcons({ nodes: [messagesContainer] });
    }
  }

  function handleFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formType = form.getAttribute("data-form-type");
    const nonce = form.getAttribute("data-nonce");
    const { submitButton, submitText, messagesContainer, originalText } =
      form._cached;

    // Client-side validation
    const requiredFields = form.querySelectorAll("[required]");
    for (let field of requiredFields) {
      if (!field.value.trim()) {
        showError(form, "Please fill in all required fields.");
        field.focus();
        return;
      }
    }

    // Email validation
    const emailField = form.querySelector('[type="email"]');
    if (emailField && emailField.value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailField.value.trim())) {
        showError(form, "Please enter a valid email address.");
        emailField.focus();
        return;
      }
    }

    // Disable submit button
    submitButton.disabled = true;
    submitText.textContent = "Sending...";

    // Clear previous messages
    messagesContainer.classList.add("hidden");
    messagesContainer.innerHTML = "";

    // Collect form data
    const formData = new FormData(form);
    formData.append("action", "bigtricks_submit_form");
    formData.append("form_type", formType);
    formData.append("nonce", nonce);

    // Send AJAX request
    fetch(bigtricksData.ajaxUrl, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((response) => response.json())
      .then((data) => {
        // Re-enable submit button
        submitButton.disabled = false;
        submitText.textContent = originalText;

        // Show message
        messagesContainer.classList.remove("hidden");

        if (data.success) {
          messagesContainer.innerHTML = `
					<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-start gap-3">
						<i data-lucide="check-circle" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
						<div>
							<p class="font-semibold">${data.data.message}</p>
							${data.data.details ? `<p class="text-sm mt-1 opacity-90">${data.data.details}</p>` : ""}
						</div>
					</div>
				`;

          // Reset form
          form.reset();

          // Re-initialize Lucide icons
          if (typeof lucide !== "undefined") {
            lucide.createIcons({ nodes: [messagesContainer] });
          }

          // Scroll to success message
          messagesContainer.scrollIntoView({
            behavior: "smooth",
            block: "nearest",
          });
        } else {
          messagesContainer.innerHTML = `
					<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-start gap-3">
						<i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
						<div>
							<p class="font-semibold">Error</p>
							<p class="text-sm mt-1">${data.data.message || "Something went wrong. Please try again."}</p>
						</div>
					</div>
				`;

          // Re-initialize Lucide icons
          if (typeof lucide !== "undefined") {
            lucide.createIcons({ nodes: [messagesContainer] });
          }
        }
      })
      .catch((error) => {
        console.error("Form submission error:", error);

        // Re-enable submit button
        submitButton.disabled = false;
        submitText.textContent = originalText;

        // Show error message
        messagesContainer.classList.remove("hidden");
        messagesContainer.innerHTML = `
				<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-start gap-3">
					<i data-lucide="x-circle" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
					<div>
						<p class="font-semibold">Network Error</p>
						<p class="text-sm mt-1">Unable to send your message. Please check your connection and try again.</p>
					</div>
				</div>
			`;

        // Re-initialize Lucide icons
        if (typeof lucide !== "undefined") {
          lucide.createIcons({ nodes: [messagesContainer] });
        }
      });
  }
});
