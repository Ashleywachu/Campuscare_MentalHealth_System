
// Generate Anonymous ID
function generateID(){
let id = "STU-" + Math.floor(Math.random()*100000);
document.getElementById("studentID").innerText =
"Anonymous ID: " + id;
}

generateID();


// Navigation Sections
function showSection(section){
document.querySelectorAll(".content")
.forEach(sec => sec.classList.remove("active"));

document.getElementById(section)
.classList.add("active");
}


// Dark Mode Toggle
function toggleTheme(){
document.body.classList.toggle("dark");
}


// Daily Affirmations Rotation
const affirmations = [
"You are stronger than you think.",
"Progress matters more than perfection.",
"Your feelings are valid.",
"Small steps still move you forward.",
"You deserve support and peace."
];

function changeAffirmation(){
let random =
affirmations[Math.floor(Math.random()*affirmations.length)];

document.getElementById("affirmationText").innerText = random;
}

changeAffirmation();
setInterval(changeAffirmation, 8000);