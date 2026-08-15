// Remplacez vos variables d'icônes par des sélecteurs d'icônes Lucide
// function updateThemeIcons(isDark) {
//     const sunIcon = document.querySelector("svg.lucide-sun");
//     const moonIcon = document.querySelector("svg.lucide-moon");

//     // Vérification de sécurité : si les éléments n'existent pas, on arrête tout
//     if (!sunIcon || !moonIcon) {
//         console.warn(
//             "Les icônes Lucide ne sont pas encore chargées dans le DOM.",
//         );
//         return;
//     }

//     if (isDark) {
//         sunIcon.classList.remove("hidden");
//         moonIcon.classList.add("hidden");
//     } else {
//         sunIcon.classList.add("hidden");
//         moonIcon.classList.remove("hidden");
//     }
// }

function updateThemeIcons(isDark) {
    const sunIcon = document.querySelector(".theme-icon-sun");
    const moonIcon = document.querySelector(".theme-icon-moon");

    if (!sunIcon || !moonIcon) {
        return;
    }

    sunIcon.classList.toggle("hidden", !isDark);
    moonIcon.classList.toggle("hidden", isDark);
}
// 1. Initialisation
const themeToggleBtn = document.getElementById("theme-toggle");

if (
    localStorage.getItem("color-theme") === "dark" ||
    (!("color-theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
    document.documentElement.classList.add("dark");
    updateThemeIcons(true);
} else {
    document.documentElement.classList.remove("dark");
    updateThemeIcons(false);
}

// 2. Événement au clic
if (themeToggleBtn) {
    themeToggleBtn.addEventListener("click", function () {
        const isDark = document.documentElement.classList.toggle("dark");
        localStorage.setItem("color-theme", isDark ? "dark" : "light");
        updateThemeIcons(isDark);
    });
}

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
