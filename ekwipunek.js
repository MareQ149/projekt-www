function getItemImageSrc(itemId) {
    const itemImages = {
        1: 'items/miecz.png',
        2: 'items/maczuga.png',
        3: 'items/mlot.png',
        4: 'items/helm_wiking.png',
        5: 'items/helm_zolnierz.png',
        6: 'items/helm_rycerz.png',
        7: 'items/klata_wiking.png',
        8: 'items/klata_rycerz.png',
        9: 'items/klata_zolnierz.png',
        10: 'items/buty_rycerz.png',
        11: 'items/buty_zolnierz.png',
        12: 'items/buty_wiking.png',
        13: 'items/znak.png',
        14: 'items/flacha.png',
        15: 'items/bombie.png',
        16: 'items/tarcza_rycerz.png',
        17: 'items/tarcza_zolnierz.png',
        18: 'items/tarcza_wiking.png'
    };
    return itemImages[itemId] || 'items/default.png';
}


document.querySelectorAll('.slot img').forEach(img => {
    const slot = img.parentElement.id;

    // Usuwanie itemów z inventory (slot1...slot10)
    if (slot.startsWith('slot')) {
        img.addEventListener('click', () => {
            if (confirm("Czy na pewno chcesz pozbyć się przedmiotu?")) {
                fetch('remove_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        slot: slot,
                        item_id: img.dataset.itemid
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Błąd: ' + data.message);
                    }
                })
                .catch(() => alert('Błąd sieci'));
            }
        });
    }

    // Draggable - Twój istniejący kod
    img.addEventListener('dragstart', e => {
        const fromSlot = e.target.parentElement.id;
        e.dataTransfer.setData('text/plain', JSON.stringify({
            itemId: e.target.dataset.itemid,
            fromSlot: fromSlot
        }));
    });
});

document.querySelectorAll('.slot').forEach(slot => {
    slot.addEventListener('dragover', e => {
        e.preventDefault();
        slot.style.outline = '2px solid yellow';
    });

    slot.addEventListener('dragleave', () => {
        slot.style.outline = '';
    });

    slot.addEventListener('drop', e => {
        e.preventDefault();
        slot.style.outline = '';

        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        const fromSlot = data.fromSlot;
        const toSlot = slot.id;
        const itemId = parseInt(data.itemId);

        fetch('update_slot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                from_slot: fromSlot,
                to_slot: toSlot,
                item_id: itemId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Błąd: ' + data.message);
            }
        })
        .catch(() => alert('Błąd sieci'));
    });
});
