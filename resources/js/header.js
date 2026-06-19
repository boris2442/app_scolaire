const themeToggleBtn = document.getElementById("theme-toggle");
const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
const themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");

// 1. Initialisation : Déterminer le mode actuel
if (
    localStorage.getItem("color-theme") === "dark" ||
    (!("color-theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
    themeToggleLightIcon.classList.remove("hidden");
    document.documentElement.classList.add("dark");
} else {
    themeToggleDarkIcon.classList.remove("hidden");
    document.documentElement.classList.remove("dark");
}

// 2. Événement au clic
themeToggleBtn.addEventListener("click", function () {
    // Toggle icons
    themeToggleDarkIcon.classList.toggle("hidden");
    themeToggleLightIcon.classList.toggle("hidden");

    // Toggle Dark Mode
    if (document.documentElement.classList.contains("dark")) {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("color-theme", "light");
    } else {
        document.documentElement.classList.add("dark");
        localStorage.setItem("color-theme", "dark");
    }
});

// function updateClock() {
//     const now = new Date();
//     const hours = String(now.getHours()).padStart(2, '0');
//     const minutes = String(now.getMinutes()).padStart(2, '0');
//     const seconds = String(now.getSeconds()).padStart(2, '0');

//     const timeString = `${hours}:${minutes}:${seconds}`;
//     document.getElementById('digital-clock').textContent = timeString;
// }

// // Lancer l'horloge immédiatement
// updateClock();
// // Mettre à jour toutes les secondes (1000ms)
// setInterval(updateClock, 1000);

let clockInterval = null;

function updateClock() {
    const clockElement = document.getElementById("digital-clock");

    // SÉCURITÉ : Si l'élément n'est pas sur la page actuelle, on ne fait rien
    if (!clockElement) return;

    const now = new Date();
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    const seconds = String(now.getSeconds()).padStart(2, "0");

    clockElement.textContent = `${hours}:${minutes}:${seconds}`;
}

// 1. Déclenché à chaque fois que Turbo charge ou affiche une page
document.addEventListener("turbo:load", () => {
    // On nettoie d'abord un ancien intervalle s'il existait
    if (clockInterval) clearInterval(clockInterval);

    // On vérifie si l'horloge est présente sur cette page avant de lancer le boucle
    if (document.getElementById("digital-clock")) {
        updateClock(); // Lance immédiatement
        clockInterval = setInterval(updateClock, 1000); // Répète toutes les secondes
    }
});

// 2. Nettoyage : Dès qu'on quitte la page courante, on coupe le timer pour libérer la mémoire
document.addEventListener("turbo:before-render", () => {
    if (clockInterval) {
        clearInterval(clockInterval);
        clockInterval = null;
    }
});
