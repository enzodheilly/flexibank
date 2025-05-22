document.addEventListener("DOMContentLoaded", function () {
    const nextButton = document.querySelector(".next-step");
    const prevButton = document.querySelector(".prev-step");
    const submitButton = document.querySelector(".submit-button"); 
    const steps = document.querySelectorAll(".form-step");
    const progressBar = document.getElementById("progress-bar");

    let currentStepIndex = 0;

    function updateButtons() {
        if (currentStepIndex === 0) {
            prevButton.style.display = "none"; // Cacher "Précédent"
            nextButton.style.display = "inline-block"; // Afficher "Suivant"
            submitButton.style.display = "none"; // Cacher "Soumettre"
        } else if (currentStepIndex === steps.length - 1) {
            prevButton.style.display = "inline-block"; // Afficher "Précédent"
            nextButton.style.display = "none"; // Cacher "Suivant"
            submitButton.style.display = "inline-block"; // Afficher "Soumettre"
        } else {
            prevButton.style.display = "inline-block"; // Afficher "Précédent"
            nextButton.style.display = "inline-block"; // Afficher "Suivant"
            submitButton.style.display = "none"; // Cacher "Soumettre"
        }
    }

    nextButton.addEventListener("click", function () {
        if (currentStepIndex < steps.length - 1) {
            steps[currentStepIndex].classList.remove("active");
            currentStepIndex++;
            steps[currentStepIndex].classList.add("active");

            const progressWidth = ((currentStepIndex + 1) / steps.length) * 100;
            progressBar.style.width = progressWidth + "%";
            progressBar.textContent = `Étape ${currentStepIndex + 1} / ${steps.length}`;

            updateButtons();
        }
    });

    prevButton.addEventListener("click", function () {
        if (currentStepIndex > 0) {
            steps[currentStepIndex].classList.remove("active");
            currentStepIndex--;
            steps[currentStepIndex].classList.add("active");

            const progressWidth = ((currentStepIndex + 1) / steps.length) * 100;
            progressBar.style.width = progressWidth + "%";
            progressBar.textContent = `Étape ${currentStepIndex + 1} / ${steps.length}`;

            updateButtons();
        }
    });

    updateButtons(); // Initialisation correcte des boutons au chargement
});

