<script>
        document.getElementById('accept-cookies').addEventListener('click', function () {
            setCookie('cookies_accepted', 'true', 365);
            document.getElementById('cookie-banner').style.display = 'none';
        });

        document.getElementById('refuse-cookies').addEventListener('click', function () {
            setCookie('cookies_accepted', 'false', 365);
            document.getElementById('cookie-banner').style.display = 'none';
        });

        document.getElementById('manage-cookies').addEventListener('click', function () {
            const gestionPage = document.createElement('div');
            gestionPage.className = 'gestion-cookies';
            gestionPage.innerHTML = `
    <div>
        <h2>Gestion de vos cookies</h2>
        <p>Options pour gérer les cookies :</p>
        <ul>
            <li>
                Cookie à des fins de fonctionnement du site (Obligatoire)
                <div class="cookie-options">
                    <button class="accept-btn" onclick="acceptCookie(this, 'fonctionnement')">Accepter</button>
                    <button class="refuse-btn" onclick="refuseCookie(this, 'fonctionnement')">Refuser</button>
                </div>
            </li>
            <li>
                Cookie à des fins d'analyses statistiques
                <div class="cookie-options">
                    <button class="accept-btn" onclick="acceptCookie(this, 'statistiques')">Accepter</button>
                    <button class="refuse-btn" onclick="refuseCookie(this, 'statistiques')">Refuser</button>
                </div>
            </li>
            <li>
                Cookies à des fins de personnalisation de l'expérience FlexiBank
                <div class="cookie-options">
                    <button class="accept-btn" onclick="acceptCookie(this, 'personnalisation')">Accepter</button>
                    <button class="refuse-btn" onclick="refuseCookie(this, 'personnalisation')">Refuser</button>
                </div>
            </li>
            <li>
                Cookies publicitaires et réseaux sociaux
                <div class="cookie-options">
                    <button class="accept-btn" onclick="acceptCookie(this, 'reseaux_sociaux')">Accepter</button>
                    <button class="refuse-btn" onclick="refuseCookie(this, 'reseaux_sociaux')">Refuser</button>
                </div>
            </li>
            <li>
                Cookies nécessaires à l'assistance à distance par "chat"
                <div class="cookie-options">
                    <button class="accept-btn" onclick="acceptCookie(this, 'assistance_a_distance')">Accepter</button>
                    <button class="refuse-btn" onclick="refuseCookie(this, 'assistance_a_distance')">Refuser</button>
                </div>
            </li>
        </ul>
        <button class="close-btn" id="close-gestion">Fermer</button>
        <button class="validate-btn" id="validate-cookies">Valider</button>
    </div>
    `;
            document.body.appendChild(gestionPage);

            document.getElementById('close-gestion').addEventListener('click', function () {
                document.body.removeChild(gestionPage);
            });

            document.getElementById('validate-cookies').addEventListener('click', function () {
                document.getElementById('cookie-banner').style.display = 'none';
                saveCookiePreferences();
                document.body.removeChild(gestionPage);
            });
        });

        function acceptCookie(button, cookieType) {
            button.classList.add('clicked');
            button.nextElementSibling.classList.remove('clicked');
            setCookie(cookieType, 'accepted', 365);
        }

        function refuseCookie(button, cookieType) {
            button.classList.add('clicked');
            button.previousElementSibling.classList.remove('clicked');
            setCookie(cookieType, 'refused', 365);
        }

        function saveCookiePreferences() {
            const cookies = ['fonctionnement', 'statistiques', 'personnalisation', 'reseaux_sociaux', 'assistance_a_distance'];

            cookies.forEach(cookie => {
                const accepted = document.querySelector(`button.accept-btn[data-cookie="${cookie}"]`).classList.contains('clicked');
                const value = accepted ? 'accepted' : 'refused';
                setCookie(cookie, value, 365);
            });
        }

        function setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            const cname = name + "=";
            const decodedCookie = decodeURIComponent(document.cookie);
            const ca = decodedCookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(cname) == 0) {
                    return c.substring(cname.length, c.length);
                }
            }
            return "";
        }

        function checkCookie() {
            const cookiesAccepted = getCookie("cookies_accepted");
            const localStorageVisited = localStorage.getItem('cookie_preferences_visited');
            if (cookiesAccepted === "true" || localStorageVisited === "true") {
                document.getElementById('cookie-banner').style.display = 'none';
            } else {
                // Vérifier les préférences individuelles de cookies
                const cookies = ['fonctionnement', 'statistiques', 'personnalisation', 'reseaux_sociaux', 'assistance_a_distance'];
                let allAccepted = true; // Vérifier si tous les types de cookies sont acceptés

                cookies.forEach(cookie => {
                    const cookieValue = getCookie(cookie);
                    if (cookieValue !== 'accepted') {
                        allAccepted = false;
                    }

                });

                if (allAccepted) {
                    document.getElementById('cookie-banner').style.display = 'none';
                } else {
                    document.getElementById('cookie-banner').style.display = 'block';
                }

                localStorage.setItem('cookie_preferences_visited', 'true');
            }



        document.addEventListener('DOMContentLoaded', (event) => {
            checkCookie();

            document.getElementById('newsletter-form').addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting normally

                // Display confirmation message
                document.getElementById('confirmation-message').style.display = 'block';

                // Automatically hide the confirmation message after 3 seconds
                setTimeout(function () {
                    document.getElementById('confirmation-message').style.display = 'none';
                }, 3000);

                // Optionally, reset the form
                // this.reset();
            });
        });



        document.getElementById('chat-bubble').addEventListener('click', function () {
            // Logic to open AI assistance chat
            window.location.href = 'chatbot.html';
        });


        document.addEventListener('DOMContentLoaded', function () {
            const chatBubble = document.getElementById('chat-bubble');
            const messageBubble = document.createElement('div');
            messageBubble.innerText = "assistant virutel";
            messageBubble.style.position = 'absolute';
            messageBubble.style.bottom = '100px';
            messageBubble.style.position = 'fixed';
            messageBubble.style.bottom = '110px';
            messageBubble.style.right = '20px';
            messageBubble.style.backgroundColor = 'grey';
            messageBubble.style.color = 'white';
            messageBubble.style.padding = '5px 10px';
            messageBubble.style.borderRadius = '5px';
            messageBubble.style.zIndex = '1003';
            window.addEventListener('scroll', () => {
                const rect = chatBubble.getBoundingClientRect();
                messageBubble.style.bottom = `${window.innerHeight - rect.top + 10}px`;
            });
            document.body.appendChild(messageBubble);

            setTimeout(() => {
                document.body.removeChild(messageBubble);
            }, 3000);
        })

</script>
