document.addEventListener("DOMContentLoaded", () => {
  const tooltip = document.getElementById("tooltip");

  function showTooltip(e) {
    const item = e.currentTarget;
    const name = item.getAttribute("alt") || "Przedmiot";
    const price = item.getAttribute("data-price") || 0;
    const hp = item.getAttribute("data-hp_bonus") || 0;
    const dmg = item.getAttribute("data-damage_bonus") || 0;
    const def = item.getAttribute("data-defense_bonus") || 0;
    const agi = item.getAttribute("data-agility_bonus") || 0;
    const luck = item.getAttribute("data-luck_bonus") || 0;
    const block = item.getAttribute("data-block_bonus") || 0;

    tooltip.style.display = "block";
    tooltip.innerHTML = `
      <strong>${name}</strong><br>
      Cena: ${price} <br>
      HP bonus: ${hp} <br>
      DMG bonus: ${dmg} <br>
      DEF bonus: ${def} <br>
      AGI bonus: ${agi} <br>
      LUCK bonus: ${luck} <br>
      BLOCK bonus: ${block}
    `;
  }

  function moveTooltip(e) {
  const tooltipHeight = tooltip.offsetHeight;
  tooltip.style.top = (e.pageY - tooltipHeight - 10) + "px";
  tooltip.style.left = (e.pageX + 15) + "px";
}

  function hideTooltip() {
    tooltip.style.display = "none";
  }


  function handleClick(e) {
    e.preventDefault();
    const item = e.currentTarget;
    const itemId = item.getAttribute("data-id");
    if (!itemId) {
      alert("Nie podano przedmiotu");
      return;
    }
    const price = item.getAttribute("data-price") || 0;
    if (confirm(`Czy chcesz kupić ten przedmiot za ${price} zł?`)) {
      fetch("purchase.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `item_id=${encodeURIComponent(itemId)}`
      })
        .then(response => response.json())
        .then(data => {
          alert(data.message);
        })
        .catch(() => alert("Błąd komunikacji z serwerem"));
    }
  }


  const items = document.querySelectorAll("img[data-id]");
  items.forEach(img => {
    img.addEventListener("mouseenter", showTooltip);
    img.addEventListener("mousemove", moveTooltip);
    img.addEventListener("mouseleave", hideTooltip);
    img.addEventListener("click", handleClick);
  });
});

document.getElementById('menuToggle').addEventListener('click', function() {
        const menu = document.getElementById('dropdownMenu');
        menu.classList.toggle('hidden');
    });
