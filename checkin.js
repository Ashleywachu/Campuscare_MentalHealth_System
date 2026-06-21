const questions = [
"I felt emotionally stable today",
"I slept well last night",
"I feel motivated academically",
"I feel socially supported",
"I can manage my stress effectively",
"I feel hopeful about the future",
"I experienced anxiety today",
"I felt overwhelmed by responsibilities",
"I was able to concentrate well",
"I feel comfortable seeking help when needed"
];

const container = document.getElementById("questions");

/* Create Questions Automatically */

questions.forEach((q, index)=>{
let label = document.createElement("label");
label.innerText = q;

let select = document.createElement("select");
select.id = "q"+index;

for(let i=1;i<=5;i++){
let option = document.createElement("option");
option.value = i;
option.text = i;
select.appendChild(option);
}

container.appendChild(label);
container.appendChild(select);
});



function submitCheckin(){

let answers = [];
let total = 0;

for(let i=0;i<questions.length;i++){
let value = parseInt(document.getElementById("q"+i).value);
answers.push(value);
total += value;
}

/* Average Score */
let average = total / questions.length;

let resultMessage = "";

/* Risk Classification */

if(average >=4){
resultMessage =
"Low Risk: Your wellbeing indicators appear positive. Continue maintaining healthy habits.";
}
else if(average >=2.5){
resultMessage =
"Moderate Risk: You may be experiencing emotional strain. Consider journaling or scheduling counselling support.";
}
else{
resultMessage =
"High Risk: The system recommends speaking with a counsellor as soon as possible.";
}

document.getElementById("resultText").innerText =
"Average Score: " + average.toFixed(2) + " | " + resultMessage;


/* Generate Graph */

const ctx = document.getElementById("resultChart").getContext("2d");

new Chart(ctx,{
type:"bar",
data:{
labels:[
"Q1","Q2","Q3","Q4","Q5",
"Q6","Q7","Q8","Q9","Q10"
],
datasets:[{
label:"Your Responses",
data:answers
}]
},
options:{
scales:{
y:{
beginAtZero:true,
max:5
}
}
}
});

}