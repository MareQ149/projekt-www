function dodajKomunikat(tresc) {
    const panel = document.getElementById("panel_glowny");
    const p = document.createElement("p");
    p.textContent = tresc;
    panel.appendChild(p);
    panel.scrollTop = panel.scrollHeight;
}

function wykonajAtak(atakujacy, broniacy, nazwaAtakujacego, nazwaBroniacego) {
    if (Math.random() * 100 < broniacy.agility) {
        dodajKomunikat(`${nazwaBroniacego} unika ataku ${nazwaAtakujacego}!`);
        return false;
    }
    if (Math.random() * 100 < broniacy.block * 10) {
        dodajKomunikat(`${nazwaBroniacego} blokuje atak ${nazwaAtakujacego}!`);
        return false;
    }
    const suroweObrazenia = atakujacy.damage - broniacy.defense;
    const finalneObrazenia = Math.max(1, suroweObrazenia);
    broniacy.hp -= finalneObrazenia;
    dodajKomunikat(`${nazwaAtakujacego} zadaje ${finalneObrazenia} obrażeń ${nazwaBroniacego}.`);
    updateHpBar(broniacy === enemy ? "enemy-hp-bar" : "player-hp-bar",
                broniacy === enemy ? "enemy-hp-text" : "player-hp-text",
                broniacy.hp, broniacy === enemy ? przeciwnik.hp : gracz.hp);
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
            dodajKomunikat(`Twoje kredyty zostały zaktualizowane o ${zmiana > 0 ? '+' : ''}${zmiana}.`);
        } else {
            dodajKomunikat('Błąd podczas aktualizacji kredytów: ' + (data.error || 'Nieznany błąd'));
        }
    })
    .catch(() => {
        dodajKomunikat('Błąd połączenia podczas aktualizacji kredytów.');
    });
}

// Inicjalizacja pasków HP na start
updateHpBar("player-hp-bar", "player-hp-text", gracz.hp, gracz.hp);
updateHpBar("enemy-hp-bar", "enemy-hp-text", przeciwnik.hp, przeciwnik.hp);
