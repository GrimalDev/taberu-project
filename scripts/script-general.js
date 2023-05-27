const main = document.querySelector('main')
const burgerMenu = document.getElementById('animated-burger')
const pulse = document.getElementById('relative-circle-animation-container')
const allCardsContainer = document.getElementsByClassName('all-generated-cards-container')[0];

burgerMenu.addEventListener("click", function () {
  burgerMenu.classList.toggle("burger-active")
});

async function pulseAnimationHandler() {
  const delay = ms => new Promise(res => setTimeout(res, ms));
  await delay(2500);
  pulse.classList.toggle("make-disappear");
  allCardsContainer.classList.toggle("make-disappear");
}

const init = async () => {
  await pulseAnimationHandler();
}

window.onload = init;

/*-------------Gestion des cartes de recettes-------------*/

