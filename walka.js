document.addEventListener('DOMContentLoaded', () => {
  const toggleButton = document.getElementById("menuToggle");
  const dropdownMenu = document.getElementById("dropdownMenu");

  toggleButton.addEventListener("click", () => {
    dropdownMenu.classList.toggle("show");
  });

  document.getElementById('findEnemyBtn').addEventListener('click', () => {
    fetch('get_player_stats.php')
      .then(res => res.json())
      .then(playerStats => {
        if(playerStats.error){
          alert(playerStats.error);
          return;
        }

        // Wyświetl statystyki postaci
        const postacSection = document.getElementById('postac');
        postacSection.innerHTML = `
          <h2>Ty</h2>
          <img src="photos/logo.jpg" alt="Ty">
          <p>HP: ${playerStats.hp}</p>
          <p>Dmg: ${playerStats.dmg}</p>
          <p>Def: ${playerStats.def}</p>
          <p>Agility: ${playerStats.agility}</p>
          <p>Luck: ${playerStats.luck}</p>
          <p>Block: ${playerStats.block}</p>
        `;

        // Przesyłamy statystyki postaci do get_enemy.php jako parametry GET
        const params = new URLSearchParams(playerStats).toString();

        fetch(`get_enemy.php?${params}`)
          .then(res => res.json())
          .then(enemy => {
            if(enemy.error){
              alert(enemy.error);
              return;
            }
            const wrogSection = document.getElementById('wrog');
            wrogSection.innerHTML = `
              <h2>${enemy.name}</h2>
              <img src="photos/${enemy.photo}" alt="${enemy.name}">
              <p>HP: ${enemy.hp}</p>
              <p>Dmg: ${enemy.dmg}</p>
              <p>Def: ${enemy.def}</p>
              <p>Agility: ${enemy.agility}</p>
              <p>Luck: ${enemy.luck}</p>
              <p>Block: ${enemy.block}</p>
            `;
          })
          .catch(() => alert('Błąd wczytywania przeciwnika'));
      })
      .catch(() => alert('Błąd wczytywania statystyk postaci'));
  });
});
