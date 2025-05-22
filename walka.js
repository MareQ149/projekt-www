const toggleButton = document.getElementById("menuToggle");
const dropdownMenu = document.getElementById("dropdownMenu");

toggleButton.addEventListener("click", () => {
    dropdownMenu.classList.toggle("hidden");
});


const walkabutton = document.getElementById("przycisk_walka");
const walkadiv = document.getElementById("walka");
const napis = document.getElementById("napis");
const menu = document.getElementById("menuToggle");
walkabutton.addEventListener("click", function() {
    walkadiv.classList.remove("hidden");
    walkabutton.classList.add("hidden");
    napis.classList.add("hidden");
    menu.classList.add("hidden");
    // Kopia statystyk gracza i przeciwnika do modyfikacji w trakcie walki
    window.player = {...gracz};
    window.enemy = {...przeciwnik};

    // Reset panelu i przycisków
    const panelGlowny = document.getElementById("panel_glowny");
    panelGlowny.innerHTML = "";
    
    if (window.btnAtakuj) {
        window.btnAtakuj.disabled = false;
        window.btnUcieczka.disabled = false;
        window.btnAtakuj.classList.remove("hidden");
        window.btnUcieczka.classList.remove("hidden");
    }
    if (window.btnZakoncz) {
        window.btnZakoncz.classList.add("hidden");
    }

    updateHpBar("player-hp-bar", "player-hp-text", player.hp, gracz.hp);
    updateHpBar("enemy-hp-bar", "enemy-hp-text", enemy.hp, przeciwnik.hp);
});

// Generowanie i wstawianie przycisków ataku i ucieczki (jeśli jeszcze nie ma)
if (!window.btnAtakuj) {
    const btnAtakuj = document.createElement("button");
    btnAtakuj.id = "btn_atakuj";
    btnAtakuj.textContent = "Atakuj";

    const btnUcieczka = document.createElement("button");
    btnUcieczka.id = "btn_ucieczka";
    btnUcieczka.textContent = "Ucieczka";

    const btnZakoncz = document.createElement("button");
    btnZakoncz.id = "btn_zakoncz";
    btnZakoncz.textContent = "Zakończ";
    btnZakoncz.classList.add("hidden");  // Na start ukryty

    const akcjeDiv = document.getElementById("akcje_walki");
    akcjeDiv.appendChild(btnAtakuj);
    akcjeDiv.appendChild(btnUcieczka);
    akcjeDiv.appendChild(btnZakoncz);

    window.btnAtakuj = btnAtakuj;
    window.btnUcieczka = btnUcieczka;
    window.btnZakoncz = btnZakoncz;

    btnAtakuj.addEventListener("click", () => {
        // Zablokuj przyciski natychmiast po kliknięciu
        btnAtakuj.disabled = true;
        btnUcieczka.disabled = true;

        wykonajAtak(player, enemy, "Ty", "Przeciwnik");
        updateHpBar("enemy-hp-bar", "enemy-hp-text", enemy.hp, przeciwnik.hp);

        if (enemy.hp <= 0) {
            dodajKomunikat("Pokonałeś przeciwnika!", "green");
            aktualizujKredyty(30);
            koniecWalki();
            return;
        }

        setTimeout(() => {
            wykonajAtak(enemy, player, "Przeciwnik", "Ty");
            updateHpBar("player-hp-bar", "player-hp-text", player.hp, gracz.hp);

            if (player.hp <= 0) {
                dodajKomunikat("Zostałeś pokonany!", "red");
                aktualizujKredyty(-10);
                koniecWalki();
                return;
            }

            // Jeśli gra trwa dalej, odblokuj przyciski do kolejnej tury
            btnAtakuj.disabled = false;
            btnUcieczka.disabled = false;
        }, 500);
    });

    btnUcieczka.addEventListener("click", () => {
        if (Math.random() < 0.5) {
            alert("Uciekłeś z walki! Tracisz 50 kredytów.");

            // Wyślij żądanie do PHP, by odjąć kredyty
            fetch("update_credits.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "change=-50"
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Odśwież stronę po aktualizacji
                } else {
                    alert("Błąd przy aktualizacji kredytów: " + (data.error || "nieznany błąd"));
                }
            })
            .catch(error => {
                alert("Błąd połączenia z serwerem: " + error);
            });

        } else {
            dodajKomunikat("Nie udało się uciec! Przeciwnik atakuje.", "red");
            btnAtakuj.disabled = true;
            btnUcieczka.disabled = true;

            setTimeout(() => {
                wykonajAtak(enemy, player, "Przeciwnik", "Ty");
                btnAtakuj.disabled = false;
                btnUcieczka.disabled = false;
            }, 1000);
        }
    });



    btnZakoncz.addEventListener("click", () => {
        // Możesz tu zrobić co chcesz, np:
        // - odświeżyć stronę
        // - ukryć panel walki i pokazać przycisk szukaj przeciwnika
        window.location.reload();
    });
}

// Funkcja wywoływana po zakończeniu walki
function koniecWalki() {
    // Ukryj przyciski atakuj i ucieczka
    btnAtakuj.classList.add("hidden");
    btnUcieczka.classList.add("hidden");

    // Pokaż przycisk zakończ
    btnZakoncz.classList.remove("hidden");
}
