document.addEventListener('DOMContentLoaded', () => {
  console.log("DOM załadowany, podpinam eventy...");

  const tooltip = document.createElement('div');
  tooltip.id = 'tooltip';
  tooltip.style.position = 'absolute';
  tooltip.style.backgroundColor = '#333';
  tooltip.style.color = '#fff';
  tooltip.style.padding = '8px';
  tooltip.style.borderRadius = '5px';
  tooltip.style.pointerEvents = 'none';
  tooltip.style.display = 'none';
  tooltip.style.zIndex = 1000;
  document.body.appendChild(tooltip);

  function bindItemEvents(container = document) {
    container.querySelectorAll('.slot img').forEach(img => {
      img.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', JSON.stringify({
          itemId: e.target.dataset.itemid,
          fromSlot: e.target.parentElement.id
        }));
      });

      img.addEventListener('mouseenter', e => {
        const item = e.target;
        tooltip.innerHTML = `
          <strong>Bonusy:</strong><br>
          HP: ${item.dataset.hpBonus}<br>
          Obrażenia: ${item.dataset.damageBonus}<br>
          Obrona: ${item.dataset.defenseBonus}<br>
          Zręczność: ${item.dataset.agilityBonus}<br>
          Szczęście: ${item.dataset.luckBonus}<br>
          Blok: ${item.dataset.blockBonus}
        `;
        tooltip.style.display = 'block';
      });

      img.addEventListener('mousemove', e => {
        tooltip.style.top = (e.pageY + 10) + 'px';
        tooltip.style.left = (e.pageX + 10) + 'px';
      });

      img.addEventListener('mouseleave', () => {
        tooltip.style.display = 'none';
      });
    });
  }

  bindItemEvents();

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
      const toSlot = slot.id;
      const fromSlot = data.fromSlot;
      const itemId = data.itemId;

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
            updateSlots();
            updateStats();
          } else {
            alert('Błąd podczas przesuwania itemu: ' + data.message);
          }
        })
        .catch(() => alert('Błąd sieci'));
    });
  });

  function updateStats() {
    fetch('get_stats.php')
      .then(res => res.json())
      .then(stats => {
        const statsDiv = document.getElementById('statystyki');
        if (statsDiv) {
          statsDiv.innerHTML = `
            <h2>Statystyki postaci</h2>
            <ul>
              <li>HP: ${stats.hp}</li>
              <li>Obrażenia: ${stats.damage}</li>
              <li>Obrona: ${stats.defense}</li>
              <li>Zręczność: ${stats.agility}</li>
              <li>Szczęście: ${stats.luck}</li>
              <li>Blok: ${stats.block}</li>
              <li>Kredytki: ${stats.credits}</li>
            </ul>
          `;
        }
      });
  }

  function updateSlots() {
    fetch('get_slots.php')
      .then(res => res.text())
      .then(html => {
        const container = document.getElementById('slotsContainer');
        if (container) {
          container.innerHTML = html;
          bindItemEvents(container);
        }
      });
  }

  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      const formData = new FormData(loginForm);
      fetch('login.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            location.href = 'stronka.php';
          } else {
            alert(data.message);
          }
        });
    });
  }

  if (registerForm) {
    registerForm.addEventListener('submit', e => {
      e.preventDefault();
      const formData = new FormData(registerForm);
      fetch('register.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Rejestracja udana, możesz się zalogować');
            location.href = 'index.html';
          } else {
            alert(data.message);
          }
        });
    });
  }
});
