function charCounter(textToCountID, counterID, nbMax) {
    let input = document.getElementById(textToCountID);
    let characterCounter = document.getElementById(counterID);
    const maxNumOfChars = nbMax;

    const countCharacters = () => {
        let numOfEnteredChars = input.value.length;
        let counter = maxNumOfChars - numOfEnteredChars;
        characterCounter.textContent = counter + `/${maxNumOfChars}`;

        if (counter < 0) {
            characterCounter.style.color = "red";
        } else if (counter < 20) {
            characterCounter.style.color = "orange";
        } else {
            characterCounter.style.color = "black";
        }
    };

    input.addEventListener("input", countCharacters);
}

charCounter("title-counter-input", "title-char-count", 30)
charCounter("description-counter-input", "description-char-count", 50)
