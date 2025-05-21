const tooltip = document.getElementById('tooltip');

document.querySelectorAll('.slot img').forEach(img => {
  img.addEventListener('mouseenter', (e) => {
  const itemId = img.getAttribute('data-itemid');
  if (!itemId || !tooltipsData[itemId]) return;
  const data = tooltipsData[itemId];

    if (!data) return;

    let content = `<strong>${data.name}</strong><br>`;
    content += `HP Bonus: ${data.hp_bonus}<br>`;
    content += `Damage Bonus: ${data.damage_bonus}<br>`;
    content += `Defense Bonus: ${data.defense_bonus}<br>`;
    content += `Agility Bonus: ${data.agility_bonus}<br>`;
    content += `Luck Bonus: ${data.luck_bonus}<br>`;
    content += `Block Bonus: ${data.block_bonus}`;

    tooltip.innerHTML = content;
    tooltip.style.display = 'block';
  });

  img.addEventListener('mousemove', (e) => {
    tooltip.style.left = e.pageX + 15 + 'px';
    tooltip.style.top = e.pageY + 15 + 'px';
  });

  img.addEventListener('mouseleave', () => {
    tooltip.style.display = 'none';
  });
});
