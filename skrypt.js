document.addEventListener('DOMContentLoaded', () => {
  console.log("DOM załadowany, podpinam eventy...");

  // Funkcje pokazujące formularze
  function showLoginForm() {
    document.getElementById("loginForm").style.display = "block";
    document.getElementById("loguj").style.display = "none";
    document.getElementById("rejestruj").style.display = "none";
  }

  function showRegisterForm() {
    document.getElementById("registerForm").style.display = "block";
    document.getElementById("loguj").style.display = "none";
    document.getElementById("rejestruj").style.display = "none";
  }

  // Podpinamy eventy do przycisków (jeśli istnieją)
  const logujBtn = document.getElementById('loguj');
  if (logujBtn) {
    logujBtn.addEventListener('click', showLoginForm);
  }

  const rejestrujBtn = document.getElementById('rejestruj');
  if (rejestrujBtn) {
    rejestrujBtn.addEventListener('click', showRegisterForm);
  }

  // Obsługa drag & drop itemów
  document.querySelectorAll('.slot img').forEach(img => {
    img.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/plain', JSON.stringify({
        itemId: e.target.dataset.itemid,
        fromSlot: e.target.parentElement.id
      }));
    });
  });

  document.querySelectorAll('.slot').forEach(slot => {
    slot.addEventListener('dragover', e => {
      e.preventDefault();
      slot.style.outline = '2px solid yellow';
    });
    slot.addEventListener('dragleave', e => {
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
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
          from_slot: fromSlot,
          to_slot: toSlot,
          item_id: itemId
        })
      })
      .then(res => res.json())
      .then(data => {
        if(data.success){
          location.reload();
        } else {
          alert('Błąd podczas przesuwania itemu: ' + data.message);
        }
      })
      .catch(() => alert('Błąd sieci'));
    });
  });
});
