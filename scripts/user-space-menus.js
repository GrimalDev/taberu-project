//select all section-title class elements
const sectionTitles = document.querySelectorAll('.section-container.dynamic > .section-title');
//loop through each section-title element
for (let i = 0; i < sectionTitles.length; i++) {
  //hide everything else in the parent container from the section-title element
  sectionTitles[i].addEventListener('click', function() {
    this.parentElement.classList.toggle('active');
  });
}