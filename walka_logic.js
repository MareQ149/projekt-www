function dodajKomunikat(tresc, kolor = "czarny") {
    const panel = document.getElementById("panel_glowny");
    const p = document.createElement("p");
    p.textContent = tresc;
    p.classList.add(kolor);
    panel.appendChild(p);
    panel.scrollTop = panel.scrollHeight;
}



function wykonajAtak(atakujacy, broniacy, nazwaAtakujacego, nazwaBroniacego) {
    if (atakujacy == player){
        var kolor = "green";
    }else {
        var kolor = "red";
    }
    //Sprawdzenie uniku
    if (Math.random() * 100 < broniacy.agility) {
        dodajKomunikat(`${nazwaBroniacego} unika ataku ${nazwaAtakujacego}!`, kolor);
        return false;
    }
    
    // Sprawdzenie bloku
    if (Math.random() * 100 < broniacy.block * 5) {
        dodajKomunikat(`${nazwaBroniacego} blokuje atak ${nazwaAtakujacego}!`, kolor);
        return false;
    }
    
    //Obliczanie obrażeń
    //Widełki ±20% na atakujacy.damage
    const minBaseDamage = Math.floor(atakujacy.damage * 0.8);
    const maxBaseDamage = Math.ceil(atakujacy.damage * 1.2);
    const losoweDamage = Math.floor(Math.random() * (maxBaseDamage - minBaseDamage + 1)) + minBaseDamage;

    //Obliczanie obrażeń po obronie
    let suroweObrazenia = losoweDamage - broniacy.defense;
    suroweObrazenia = Math.max(1, suroweObrazenia);
        
    //Sprawdzenie krytyka
    let czyKryt = Math.random() * 100 < atakujacy.luck * 5;
    if (czyKryt) {
        suroweObrazenia *= 2;
        dodajKomunikat(`KRYTYCZNY ATAK! ${nazwaAtakujacego} zadaje podwójne obrażenia!`, "orange");
    }
    
    //Zadawanie obrażeń
    broniacy.hp -= suroweObrazenia;
    dodajKomunikat(`${nazwaAtakujacego} zadaje ${suroweObrazenia} obrażeń ${nazwaBroniacego}.`, kolor);
    
    //Aktualizacja paska życia
    updateHpBar(
        broniacy === enemy ? "enemy-hp-bar" : "player-hp-bar",
        broniacy === enemy ? "enemy-hp-text" : "player-hp-text",
        broniacy.hp,
        broniacy === enemy ? przeciwnik.hp : gracz.hp
    );
    
    return true;
}


function updateHpBar(idBar, idText, currentHp, maxHp) {
    const bar = document.getElementById(idBar);
    const text = document.getElementById(idText);

    const percent = maxHp > 0 ? (currentHp / maxHp) * 100 : 0;
    bar.style.width = percent + '%';
    text.textContent = currentHp + " / " + maxHp;
}

function aktualizujKredyty(zmiana) {
    fetch('update_credits.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'change=' + encodeURIComponent(zmiana)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            dodajKomunikat(`Twoje kredyty zostały zaktualizowane o ${zmiana > 0 ? '+' : ''}${zmiana}.`, "black");
        } else {
            dodajKomunikat('Błąd podczas aktualizacji kredytów: ' + (data.error || 'Nieznany błąd', "black"));
        }
    })
    .catch(() => {
        dodajKomunikat('Błąd połączenia podczas aktualizacji kredytów.', "black");
    });
}

//Inicjalizacja pasków HP na start
updateHpBar("player-hp-bar", "player-hp-text", gracz.hp, gracz.hp);
updateHpBar("enemy-hp-bar", "enemy-hp-text", przeciwnik.hp, przeciwnik.hp);
