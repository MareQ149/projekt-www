function showLoginForm() {
  document.getElementById("loginForm").style.display = "block";
  document.getElementById("registerForm").style.display = "none";
  document.getElementById("loguj").style.display = "none";
  document.getElementById("rejestruj").style.display = "none";
}

function showRegisterForm() {
  document.getElementById("registerForm").style.display = "block";
  document.getElementById("loginForm").style.display = "none";
  document.getElementById("loguj").style.display = "none";
  document.getElementById("rejestruj").style.display = "none";
}

//Logowanie
document.getElementById("loginFormElement").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("logowanie.php", {
    method: "POST",
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.success) {
        window.location.href = "stronka.php";
      }
    })
    .catch(() => alert("Błąd sieci"));
});

//Rejestracja
document.getElementById("registerFormElement").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("rejestracja.php", {
    method: "POST",
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.success) {
        window.location.href = "index.html";
      }
    })
    .catch(() => alert("Błąd sieci"));
});
