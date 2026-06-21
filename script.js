

/*SAMPLE DATA*/

let users = [
    { id:"STU-104522", name:"A. Kimani", role:"Student", status:"active" },
    { id:"STU-872134", name:"J. Otieno", role:"Student", status:"active" },
    { id:"STU-551290", name:"L. Wanjiru", role:"Student", status:"deactivated" },
    { id:"CNS-200145", name:"Dr. Sarah Mwangi", role:"Counselor", status:"active" },
    { id:"CNS-200871", name:"Mr. James Achieng", role:"Counselor", status:"active" },
    { id:"DNS-300120", name:"Prof. R. Otieno", role:"Dean", status:"active" },
];

let logins = [
    { name:"A. Kimani", role:"Student", time:"Jun 18, 2026 — 9:14 AM", device:"Chrome · Windows" },
    { name:"Dr. Sarah Mwangi", role:"Counselor", time:"Jun 18, 2026 — 8:52 AM", device:"Safari · macOS" },
    { name:"J. Otieno", role:"Student", time:"Jun 18, 2026 — 8:10 AM", device:"App · Android" },
    { name:"Mr. James Achieng", role:"Counselor", time:"Jun 17, 2026 — 6:45 PM", device:"Chrome · macOS" },
    { name:"Prof. R. Otieno", role:"Dean", time:"Jun 17, 2026 — 4:02 PM", device:"Edge · Windows" },
];

let resources = [
    { title:"Managing Exam Stress", type:"Article", added:"Jun 12, 2026" },
    { title:"5-Minute Breathing Reset", type:"Audio", added:"Jun 8, 2026" },
    { title:"Sleep & Study Balance", type:"Video", added:"May 29, 2026" },
];

let idCounter = 900;

/*NAVIGATION*/
/* Each nav item has data-target="dashboard" / "users" / etc.
   Clicking one shows the matching <section id="..."> and hides the rest. */

const titles = {
    dashboard:["Dashboard","A live overview of CampusCare activity."],
    users:["User Management","Add, edit, or remove accounts."],
    logins:["Login Activity","See who's signing in, and when."],
    assignments:["Counselor Assignment","Match students with the right counselor."],
    monitoring:["System Monitoring","Server and database health at a glance."],
    resources:["Resource Management","Publish and maintain student-facing resources."],
    reports:["Reports & Analytics","Snapshot the platform's current state."],
    backup:["Backup & Recovery","Protect platform data."],
    announcements:["Announcements","Message every student and counselor."],
    profile:["Profile Settings","Manage your admin account."]
};

document.querySelectorAll(".nav-links li[data-target]").forEach(li=>{
    li.addEventListener("click", ()=>showSection(li.dataset.target));
    li.addEventListener("keydown", e=>{
        if(e.key === "Enter" || e.key === " "){
            e.preventDefault();
            showSection(li.dataset.target);
        }
    });
});

function showSection(section){

    document.querySelectorAll(".content").forEach(sec=>sec.classList.remove("active"));
    document.getElementById(section).classList.add("active");

    document.querySelectorAll(".nav-links li[data-target]").forEach(li=>{
        li.classList.toggle("active", li.dataset.target === section);
    });

    const t = titles[section];
    if(t){
        document.getElementById("pageTitle").textContent = t[0];
        document.getElementById("pageSubtitle").textContent = t[1];
    }
}

function logout(){
    window.location.href = "Home.html";
}

/*CLOCK*/

function tickClock(){
    const now = new Date();
    document.getElementById("clock").textContent = now.toLocaleString(undefined,{
        weekday:"short", month:"short", day:"numeric",
        hour:"2-digit", minute:"2-digit"
    });
}
tickClock();
setInterval(tickClock, 30000);

/*RENDER: USERS*/

function renderUsers(){

    const body = document.getElementById("userTableBody");
    body.innerHTML = "";

    users.forEach((u, i)=>{
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td class="id">${u.id}</td>
            <td>${u.name}</td>
            <td>${u.role}</td>
            <td><span class="badge ${u.status}">${u.status === "active" ? "Active" : "Deactivated"}</span></td>
            <td>
                <div class="row-actions">
                    <button class="btn ghost small" onclick="editUser(${i})">Edit</button>
                    <button class="btn ghost small" onclick="toggleStatus(${i})">${u.status === "active" ? "Deactivate" : "Activate"}</button>
                    <button class="btn danger small" onclick="deleteUser(${i})">Delete</button>
                </div>
            </td>
        `;
        body.appendChild(tr);
    });

    renderStats();
}

function addUser(){
    const name = prompt("Full name:");
    if(!name) return;
    const role = prompt("Role (Student / Counselor / Dean / Admin):", "Student");
    if(!role) return;

    idCounter++;
    const r = role.trim().toLowerCase();
    let prefix = "STU";
    if(r.startsWith("counsel")) prefix = "CNS";
    else if(r.startsWith("dean")) prefix = "DNS";
    else if(r.startsWith("admin")) prefix = "ADM";

    users.push({ id:`${prefix}-${idCounter}`, name, role, status:"active" });
    renderUsers();
}

function editUser(i){
    const name = prompt("Full name:", users[i].name);
    if(!name) return;
    users[i].name = name;
    renderUsers();
}

function toggleStatus(i){
    users[i].status = users[i].status === "active" ? "deactivated" : "active";
    renderUsers();
}

function deleteUser(i){
    if(!confirm(`Remove ${users[i].name} (${users[i].id})? This can't be undone.`)) return;
    users.splice(i,1);
    renderUsers();
}


/*RENDER: STATS + DONUT*/

function renderStats(){

    const students = users.filter(u=>u.role==="Student").length;
    const counselors = users.filter(u=>u.role==="Counselor").length;
    const active = users.filter(u=>u.status==="active").length;
    const deactivated = users.filter(u=>u.status==="deactivated").length;
    const total = users.length || 1;

    document.getElementById("statStudents").textContent = students;
    document.getElementById("statCounselors").textContent = counselors;
    document.getElementById("statActive").textContent = active;
    document.getElementById("statDeactivated").textContent = deactivated;

    document.getElementById("legendActive").textContent = active;
    document.getElementById("legendDeactivated").textContent = deactivated;
    document.getElementById("donutTotal").textContent = users.length;

    const r = 62;
    const circumference = 2 * Math.PI * r;
    const activeLen = (active/total) * circumference;
    const deactivatedLen = (deactivated/total) * circumference;

    const activeCircle = document.getElementById("donutActive");
    const deactivatedCircle = document.getElementById("donutDeactivated");

    activeCircle.setAttribute("stroke-dasharray", `${activeLen} ${circumference-activeLen}`);
    activeCircle.setAttribute("stroke-dashoffset", "0");

    deactivatedCircle.setAttribute("stroke-dasharray", `${deactivatedLen} ${circumference-deactivatedLen}`);
    deactivatedCircle.setAttribute("stroke-dashoffset", `${-activeLen}`);
}

/*RENDER: LOGINS*/

function renderLogins(){
    const body = document.getElementById("loginTableBody");
    body.innerHTML = "";

    logins.forEach(l=>{
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${l.name}</td>
            <td>${l.role}</td>
            <td class="time">${l.time}</td>
            <td>${l.device}</td>
        `;
        body.appendChild(tr);
    });

    const todayCount = logins.filter(l=>l.time.startsWith("Jun 18")).length;
    document.getElementById("statLoginsToday").textContent = todayCount;
}

/*RENDER: RESOURCES*/

function renderResources(){
    const body = document.getElementById("resourceTableBody");
    body.innerHTML = "";

    resources.forEach((r,i)=>{
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${r.title}</td>
            <td>${r.type}</td>
            <td class="time">${r.added}</td>
            <td>
                <div class="row-actions">
                    <button class="btn ghost small" onclick="editResource(${i})">Edit</button>
                    <button class="btn danger small" onclick="deleteResource(${i})">Delete</button>
                </div>
            </td>
        `;
        body.appendChild(tr);
    });
}

function uploadResource(){
    const title = document.getElementById("resourceTitle").value.trim();
    const type = document.getElementById("resourceType").value;

    if(!title){
        alert("Add a title before uploading.");
        return;
    }

    const today = new Date().toLocaleDateString(undefined,{month:"short", day:"numeric", year:"numeric"});
    resources.unshift({ title, type, added: today });

    document.getElementById("resourceTitle").value = "";
    document.getElementById("resourceFile").value = "";
    renderResources();
}

function editResource(i){
    const title = prompt("Resource title:", resources[i].title);
    if(!title) return;
    resources[i].title = title;
    renderResources();
}

function deleteResource(i){
    if(!confirm(`Remove "${resources[i].title}"?`)) return;
    resources.splice(i,1);
    renderResources();
}

/*ASSIGNMENT*/

function assignCounselor(){
    const student = document.getElementById("assignStudent").value;
    const counselor = document.getElementById("assignCounselor").value;
    alert(`${counselor} has been assigned to ${student}.`);
}

/*REPORTS*/

function generateReport(){
    const active = users.filter(u=>u.status==="active").length;
    const deactivated = users.filter(u=>u.status==="deactivated").length;
    const todayCount = logins.filter(l=>l.time.startsWith("Jun 18")).length;

    document.getElementById("repTotal").textContent = users.length;
    document.getElementById("repActive").textContent = active;
    document.getElementById("repDeactivated").textContent = deactivated;
    document.getElementById("repLogins").textContent = todayCount;
    document.getElementById("repTime").textContent = new Date().toLocaleString();

    document.getElementById("reportPreview").classList.add("show");
}

function exportReport(){
    if(!document.getElementById("reportPreview").classList.contains("show")){
        generateReport();
    }
    window.print();
}

/*7. BACKUP */

function createBackup(){
    document.getElementById("backupStatus").textContent = `Last backup: ${new Date().toLocaleString()}`;
    alert("Backup created.");
}

function restoreBackup(){
    if(confirm("Restore the database from the last backup? Unsaved changes since then will be lost.")){
        alert("Database restored.");
    }
}

/* ANNOUNCEMENTS*/

function sendAnnouncement(){
    const text = document.getElementById("announcementText").value.trim();
    if(!text){
        alert("Write something before sending.");
        return;
    }
    const item = document.createElement("p");
    item.style.fontSize = "12px";
    item.style.color = "var(--text-muted)";
    item.style.marginTop = "10px";
    item.textContent = `Sent ${new Date().toLocaleString()} — "${text}"`;
    document.getElementById("announcementHistory").prepend(item);
    document.getElementById("announcementText").value = "";
}

/*CHART SETUP*/

new Chart(document.getElementById("systemChart"), {
    type: "bar",
    data: {
        labels: ["Mon","Tue","Wed","Thu","Fri"],
        datasets: [{
            label: "Active Users",
            data: [120,190,300,250,389],
            backgroundColor: "#7c3aed",
            borderRadius: 6
        }]
    },
    options: {
        responsive:true,
        plugins:{ legend:{ display:false } },
        scales:{ y:{ beginAtZero:true, grid:{ color:"#f1e8fd" } }, x:{ grid:{ display:false } } }
    }
});

new Chart(document.getElementById("uptimeChart"), {
    type: "line",
    data: {
        labels: ["Mon","Tue","Wed","Thu","Fri"],
        datasets: [{
            label: "Uptime %",
            data: [99.9,100,100,99.8,100],
            borderColor:"#241433",
            backgroundColor:"rgba(124,58,237,0.08)",
            tension:0.35,
            fill:true,
            pointBackgroundColor:"#7c3aed"
        }]
    },
    options: {
        responsive:true,
        plugins:{ legend:{ display:false } },
        scales:{ y:{ min:99, max:100, grid:{ color:"#f1e8fd" } }, x:{ grid:{ display:false } } }
    }
});

/*INIT */

renderUsers();
renderLogins();
renderResources();