async function pulseAnimationHandler() {
  const main = document.querySelector('main')
  const pulse = document.getElementById('relative-circle-animation-container')
  const allCardsContainer = document.getElementsByClassName('all-generated-cards-container')[0];

  const delay = ms => new Promise(res => setTimeout(res, ms));
  await delay(2500);
  pulse.classList.toggle("make-disappear");
  allCardsContainer.classList.toggle("make-disappear");
}

const init = async () => {
  await pulseAnimationHandler();
}

window.onload = init;