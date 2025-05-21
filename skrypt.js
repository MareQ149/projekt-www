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

document.getElementById("registerFormElement").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("rejestracja.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        window.location.href = "index.html";
      } else {
        alert("Błąd: " + data.message);
      }
    })
    .catch(() => alert("Błąd sieci"));
});
