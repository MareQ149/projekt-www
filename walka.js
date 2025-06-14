document.getElementById('menuToggle').addEventListener('click', function() {
        const menu = document.getElementById('dropdownMenu');
        menu.classList.toggle('hidden');
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
    window.player = {...gracz};
    window.enemy = {...przeciwnik};


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

//Generowanie i wstawianie przycisków ataku i ucieczki
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
    btnZakoncz.classList.add("hidden");

    const akcjeDiv = document.getElementById("akcje_walki");
    akcjeDiv.appendChild(btnAtakuj);
    akcjeDiv.appendChild(btnUcieczka);
    akcjeDiv.appendChild(btnZakoncz);

    window.btnAtakuj = btnAtakuj;
    window.btnUcieczka = btnUcieczka;
    window.btnZakoncz = btnZakoncz;

    btnAtakuj.addEventListener("click", () => {
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

            btnAtakuj.disabled = false;
            btnUcieczka.disabled = false;
        }, 500);
    });

    btnUcieczka.addEventListener("click", () => {
        if (Math.random() < 0.5) {
            alert("Uciekłeś z walki! Tracisz 50 kredytów.");
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
                    location.reload(); 
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
        window.location.reload();
    });
}

//Funkcja wywoływana po zakończeniu walki
function koniecWalki() {
    btnAtakuj.classList.add("hidden");
    btnUcieczka.classList.add("hidden");
    btnZakoncz.classList.remove("hidden");
}
