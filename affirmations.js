const affirmations = [
"You are doing better than you think.",
"Your presence matters.",
"Progress takes time and patience.",
"You are allowed to rest.",
"Growth happens quietly.",
"You are stronger than this moment.",
"Healing is a journey, not a race.",
"Today is another opportunity to begin again."
];

let index = 0;

const text = document.getElementById("affirmationText");

function changeAffirmation(){
    text.textContent = affirmations[index];
    index = (index + 1) % affirmations.length;
}

changeAffirmation();

setInterval(changeAffirmation, 5000);