document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ JS chargé !");

  const currentPwd = document.getElementById("current_password");
  const newPwd = document.getElementById("new_password");
  const confirmPwd = document.getElementById("confirm_password");
  const submitBtn = document.querySelector("form button[type='submit']");
  const errorDiv = document.querySelector(".current-password-error");
  const form = document.querySelector("form");
  const verifyPasswordUrl = form.dataset.verifyPasswordUrl;

  const rules = {
    length: { element: document.getElementById("length-rule"), test: (v) => v.length >= 8 },
    uppercase: { element: document.getElementById("uppercase-rule"), test: (v) => /[A-Z]/.test(v) },
    lowercase: { element: document.getElementById("lowercase-rule"), test: (v) => /[a-z]/.test(v) },
    number: { element: document.getElementById("number-rule"), test: (v) => /[0-9]/.test(v) },
    special: { element: document.getElementById("special-rule"), test: (v) => /[^A-Za-z0-9]/.test(v) },
    match: {
      element: document.getElementById("match-rule"),
      test: () => newPwd.value.trim() === confirmPwd.value.trim()
    }
  };

  function updatePasswordRules() {
    const value = newPwd.value;
    for (const ruleKey in rules) {
      const { element, test } = rules[ruleKey];
      const isValid = test(value);
      element.classList.toggle("fa-circle-check", isValid);
      element.classList.toggle("fa-circle-xmark", !isValid);
      element.classList.toggle("valid", isValid);
      element.classList.toggle("invalid", !isValid);
    }
  }

  function validateForm() {
    const pwd1 = newPwd.value.trim();
    const pwd2 = confirmPwd.value.trim();
    const current = currentPwd.value.trim();

    const isValid = Object.values(rules).every(rule => rule.test(pwd1)) && pwd1 === pwd2 && pwd1 !== current;

    submitBtn.disabled = !isValid;
    submitBtn.style.opacity = isValid ? "1" : "0.6";
  }

  newPwd.addEventListener("input", () => {
    updatePasswordRules();
    validateForm();
  });

  confirmPwd.addEventListener("input", () => {
    updatePasswordRules();
    validateForm();
  });

  currentPwd.addEventListener("input", validateForm);

  newPwd.addEventListener("focus", () => {
    const currentPassword = currentPwd.value.trim();
    if (currentPassword === "") return;

    fetch(verifyPasswordUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: new URLSearchParams({ current_password: currentPassword })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          errorDiv.style.display = "none";
          errorDiv.textContent = "";
        } else {
          errorDiv.style.display = "block";
          errorDiv.textContent = data.message;
          currentPwd.focus();
        }
      })
      .catch(err => {
        console.error("Erreur lors de la vérification du mot de passe :", err);
      });
  });

  document.querySelectorAll(".password-toggle").forEach(toggle => {
    toggle.addEventListener("click", () => {
      const input = toggle.previousElementSibling;
      const isHidden = input.type === "password";
      input.type = isHidden ? "text" : "password";
      toggle.classList.toggle("fa-eye", !isHidden);
      toggle.classList.toggle("fa-eye-slash", isHidden);
    });
  });

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const overlay = document.getElementById('loading-overlay');
    const newPassword = newPwd.value;
    const confirmPassword = confirmPwd.value;

    if (errorDiv && errorDiv.style.display !== "none") return;
    if (newPassword !== confirmPassword) {
      alert("Les mots de passe ne correspondent pas.");
      return;
    }

    overlay.style.display = "flex";
    setTimeout(() => form.submit(), 500);
  });
});
