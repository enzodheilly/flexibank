document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("form_prefix");
    const flagImg = document.getElementById("flag");

    select.addEventListener("change", function () {
        const selectedOption = select.options[select.selectedIndex];
        const imgUrl = selectedOption.getAttribute("data-img").replace('/24/', '/h40/');

        
        // Met à jour l'image du drapeau
        flagImg.src = imgUrl;

        flagImg.setAttribute("referrerpolicy", "no-referrer");

        // Met à jour le placeholder du numéro
        const input = document.querySelector("input[name='form[numeroTelephone]']");
        input.setAttribute("placeholder", `Votre numéro (${selectedOption.value})`);
    });
});