function getItemImageSrc(itemId) {
    const itemImages = {
        1: 'items/sword.png',
        2: 'items/shield.png',
        3: 'items/helmet.png',
        4: 'items/boots.png',
        5: 'items/armor.png',
        // inne...
    };
    return itemImages[itemId] || 'items/default.png';
}

document.querySelectorAll('.slot img').forEach(img => {
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
                location.reload(); // szybkie odświeżenie — możesz tu wprowadzić soft reload
            } else {
                alert('Błąd: ' + data.message);
            }
        })
        .catch(() => alert('Błąd sieci'));
    });
});
