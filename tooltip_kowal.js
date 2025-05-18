const tooltip = document.getElementById('tooltip');

document.querySelectorAll('#sklep img').forEach(img => {
  img.addEventListener('mouseenter', () => {
    const price = img.getAttribute('data-price');
    if (!price) {
      tooltip.style.display = 'none';
      return;
    }

    const hp = img.getAttribute('data-hp_bonus') || 0;
    const dmg = img.getAttribute('data-damage_bonus') || 0;
    const def = img.getAttribute('data-defense_bonus') || 0;
    const agi = img.getAttribute('data-agility_bonus') || 0;
    const luck = img.getAttribute('data-luck_bonus') || 0;
    const block = img.getAttribute('data-block_bonus') || 0;

    tooltip.innerHTML = `
      <strong>Cena: ${price}</strong><br>
      HP Bonus: ${hp}<br>
      Damage Bonus: ${dmg}<br>
      Defense Bonus: ${def}<br>
      Agility Bonus: ${agi}<br>
      Luck Bonus: ${luck}<br>
      Block Bonus: ${block}
    `;
    tooltip.style.display = 'block';
  });

  img.addEventListener('mousemove', (e) => {
    tooltip.style.left = (e.pageX + 15) + 'px';
    tooltip.style.top = (e.pageY + 15) + 'px';
  });

  img.addEventListener('mouseleave', () => {
    tooltip.style.display = 'none';
  });
});
