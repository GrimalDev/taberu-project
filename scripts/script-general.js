const burgerMenu = document.getElementById('animated-burger')
const pulse = document.getElementById('relative-circle-animation-container')
const allCardsContainer = document.getElementById('all-generated-cards-container')

burgerMenu.addEventListener("click", function () {
    burgerMenu.classList.toggle("burger-active")
});

async function removePulse() {
    const delay = ms => new Promise(res => setTimeout(res, ms));
    await delay(3500);
    pulse.classList.toggle("make-disappear");
    allCardsContainer.classList.toggle("make-disappear");
}

removePulse()

/*-------------Gestion des cartes de recettes-------------*/

