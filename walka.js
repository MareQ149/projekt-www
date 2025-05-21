const toggleButton = document.getElementById("menuToggle");
const dropdownMenu = document.getElementById("dropdownMenu");

toggleButton.addEventListener("click", () => {
    dropdownMenu.classList.toggle("show");});

const walkabutton = document.getElementById("przycisk_walka");
const walkadiv = document.getElementById("walka");

walkabutton.addEventListener("click", function(){
    console.log("Kliknięto przycisk walki");
    walkadiv.classList.remove("hidden");});