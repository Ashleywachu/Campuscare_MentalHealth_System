let users = [];
let logins = [];
let resources = [];

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
    messages:["Contact Messages","Submissions from the public Contact page."],
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

    // Refresh live request data whenever the Assign tab is opened
    if(section === "assignments"){
        loadCounselorRequests();
    }
    if(section === "users"){
        loadUsers();
    }
    if(section === "logins"){
        loadLoginActivity();
    }
    if(section === "announcements"){
        loadAnnouncements();
    }
    if(section === "messages"){
        loadContactMessages();
    }
    if(section === "resources"){
        loadMyResources();
    }
}

function logout(){
    fetch('logout.php').finally(() => {
        window.location.href = "admin-login.html";
    });
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

/*USERS — live data, read directly from students/counselors/dean/admins tables*/

function loadUsers(){
    fetch('get_all_users.php')
        .then(res => res.json())
        .then(data => {
            if(!data.success){
                document.getElementById("userTableBody").innerHTML =
                    '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">Could not load users.</td></tr>';
                return;
            }
            users = data.users;
            renderUsers();
        })
        .catch(() => {
            document.getElementById("userTableBody").innerHTML =
                '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">Could not load users.</td></tr>';
        });
}

function renderUsers(){

    const body = document.getElementById("userTableBody");

    if(!users.length){
        body.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">No users found.</td></tr>';
        renderStats();
        return;
    }

    body.innerHTML = users.map(u => `
        <tr>
            <td class="id">${u.user_id}</td>
            <td>${u.full_name}</td>
            <td>${u.role}</td>
            <td>${u.email}</td>
        </tr>
    `).join('');

    renderStats();
}


/*RENDER: STATS + DONUT*/

function renderStats(){

    const students = users.filter(u=>u.role==="Student").length;
    const counselors = users.filter(u=>u.role==="Counselor").length;
    const total = users.length || 1;
    // No deactivation feature exists yet — every account read from the DB counts as active.
    const active = users.length;
    const deactivated = 0;

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

/*LOGIN ACTIVITY — live data from login_logs, records every real login attempt*/

function loadLoginActivity(){
    fetch('get_login_activity.php')
        .then(res => res.json())
        .then(data => {
            const body = document.getElementById("loginTableBody");

            if(!data.success){
                body.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">Could not load login activity.</td></tr>';
                return;
            }

            logins = data.recent;
            document.getElementById("statLoginsToday").textContent = data.logins_today;
            document.getElementById("statFailedLogins").textContent = data.failed_24h;

            const alertCard = document.getElementById("securityAlertCard");
            if(data.failed_24h > 0){
                alertCard.style.display = "block";
                document.getElementById("securityAlertText").textContent =
                    `${data.failed_24h} failed login attempt${data.failed_24h !== 1 ? "s" : ""} detected in the last 24 hours.`;
            } else {
                alertCard.style.display = "none";
            }

            if(!logins.length){
                body.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">No login activity recorded yet.</td></tr>';
                return;
            }

            body.innerHTML = logins.map(l => `
                <tr>
                    <td>${l.display_name || l.identifier}</td>
                    <td>${l.role.charAt(0).toUpperCase() + l.role.slice(1)}</td>
                    <td class="time">${new Date(l.created_at).toLocaleString()}</td>
                    <td>${l.success ? l.device : `<span class="badge deactivated">Failed</span> ${l.device}`}</td>
                </tr>
            `).join('');
        })
        .catch(() => {
            document.getElementById("loginTableBody").innerHTML =
                '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">Could not load login activity.</td></tr>';
        });
}

/*RESOURCES — live, posted with a real link (YouTube, articles, etc.)*/

function loadMyResources(){
    fetch('get_my_resources.php')
        .then(res => res.json())
        .then(data => {
            const body = document.getElementById("resourceTableBody");
            if(!data.success || !data.resources.length){
                body.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">No resources published yet.</td></tr>';
                return;
            }
            resources = data.resources;
            body.innerHTML = resources.map(r => `
                <tr>
                    <td>${r.title}</td>
                    <td>${r.resource_type}</td>
                    <td><a href="${r.url}" target="_blank" rel="noopener" style="color:var(--accent);">${r.url}</a></td>
                    <td class="time">${new Date(r.created_at).toLocaleDateString()}</td>
                    <td>
                        <div class="row-actions">
                            <button class="btn danger small" onclick="deleteResource(${r.id})">Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        })
        .catch(() => {
            document.getElementById("resourceTableBody").innerHTML =
                '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Could not load resources.</td></tr>';
        });
}

function uploadResource(){
    const title = document.getElementById("resourceTitle").value.trim();
    const type = document.getElementById("resourceType").value;
    const url = document.getElementById("resourceUrl").value.trim();
    const description = document.getElementById("resourceDescription").value.trim();

    if(!title || !url){
        alert("Add a title and a link first.");
        return;
    }

    const formData = new FormData();
    formData.append('title', title);
    formData.append('resource_type', type);
    formData.append('url', url);
    formData.append('description', description);

    fetch('post_resource.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                document.getElementById("resourceTitle").value = "";
                document.getElementById("resourceUrl").value = "";
                document.getElementById("resourceDescription").value = "";
                loadMyResources();
            } else {
                alert(data.message || "Could not post the resource.");
            }
        })
        .catch(() => alert("Could not post the resource. Please try again."));
}

function deleteResource(resourceId){
    if(!confirm("Remove this resource?")) return;

    const formData = new FormData();
    formData.append('resource_id', resourceId);

    fetch('delete_resource.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) loadMyResources();
            else alert(data.message || "Could not remove the resource.");
        })
        .catch(() => alert("Could not remove the resource. Please try again."));
}

/*COUNSELOR ASSIGNMENT — live data from counselor_requests via PHP*/

let counselorsList = []; // populated by loadCounselorRequests()

function loadCounselorRequests(){
    fetch('get_counselor_request.php')
        .then(res => res.json())
        .then(data => {
            if(!data.success){
                document.getElementById("pendingRequestsWrap").innerHTML =
                    '<p style="font-size:13px;color:var(--text-muted);">Could not load requests.</p>';
                document.getElementById("allRequestsBody").innerHTML =
                    '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Could not load requests.</td></tr>';
                return;
            }

            counselorsList = data.counselors; // [{staff_id, title, full_name, department}]
            renderPendingRequests(data.requests);
            renderAllRequests(data.requests);

            const pendingCount = data.requests.filter(r => r.status === 'pending').length;
            document.getElementById("statPending").textContent = pendingCount;
        })
        .catch(() => {
            document.getElementById("pendingRequestsWrap").innerHTML =
                '<p style="font-size:13px;color:var(--text-muted);">Could not load requests.</p>';
            document.getElementById("allRequestsBody").innerHTML =
                '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Could not load requests.</td></tr>';
        });
}

function counselorOptionsHtml(){
    return counselorsList.map(c =>
        `<option value="${c.staff_id}">${c.title || ''} ${c.full_name} — ${c.department || ''}</option>`
    ).join('');
}

function renderPendingRequests(requests){
    // pending = brand new, never assigned. declined = bounced back, needs reassignment.
    const needsAssignment = requests.filter(r => r.status === 'pending' || r.status === 'declined');
    const wrap = document.getElementById("pendingRequestsWrap");

    if(!needsAssignment.length){
        wrap.innerHTML = '<p style="font-size:13px;color:var(--text-muted);padding:8px 0;">No requests waiting on assignment.</p>';
        return;
    }

    wrap.innerHTML = `
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Assign Counselor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                ${needsAssignment.map(r => `
                    <tr>
                        <td>${r.student_name}<br><span class="time" style="font-size:11px;">${r.admission_no}</span></td>
                        <td style="max-width:240px;">${r.message || '<span style="color:var(--text-muted);">No message</span>'}</td>
                        <td><span class="badge ${r.status === 'declined' ? 'deactivated' : 'pending'}">${r.status === 'declined' ? 'Declined — needs reassignment' : 'Pending'}</span></td>
                        <td>
                            <select id="assignSelect-${r.id}" style="min-width:200px;">
                                <option value="">— Select counselor —</option>
                                ${counselorOptionsHtml()}
                            </select>
                        </td>
                        <td>
                            <button class="btn primary small" onclick="assignCounselor(${r.id})">Assign</button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderAllRequests(requests){
    const body = document.getElementById("allRequestsBody");

    if(!requests.length){
        body.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No requests yet.</td></tr>';
        return;
    }

    const statusBadge = {
        pending:  '<span class="badge pending">Pending</span>',
        assigned: '<span class="badge active">Assigned — awaiting counselor</span>',
        accepted: '<span class="badge active">Accepted</span>',
        declined: '<span class="badge deactivated">Declined</span>',
    };

    body.innerHTML = requests.map(r => `
        <tr>
            <td>${r.student_name}<br><span class="time" style="font-size:11px;">${r.admission_no}</span></td>
            <td style="max-width:240px;">${r.message || '<span style="color:var(--text-muted);">No message</span>'}</td>
            <td>${statusBadge[r.status] || r.status}</td>
            <td>${r.counselor_name ? (r.counselor_title || '') + ' ' + r.counselor_name : '<span style="color:var(--text-muted);">—</span>'}</td>
            <td class="time">${r.requested_at ? new Date(r.requested_at).toLocaleDateString() : '—'}</td>
        </tr>
    `).join('');
}

function assignCounselor(requestId){
    const select = document.getElementById(`assignSelect-${requestId}`);
    const staffId = select ? select.value : '';

    if(!staffId){
        alert("Please select a counselor first.");
        return;
    }

    const formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('staff_id', staffId);

    fetch('assign_counselor.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadCounselorRequests(); // refresh both tables
        })
        .catch(() => alert("Could not assign the counselor. Please try again."));
}

/*REPORTS*/

function generateReport(){
    const active = users.length;
    const deactivated = 0;
    const todayCount = parseInt(document.getElementById("statLoginsToday").textContent, 10) || 0;
    const pendingCount = parseInt(document.getElementById("statPending").textContent, 10) || 0;

    document.getElementById("repTotal").textContent = users.length;
    document.getElementById("repActive").textContent = active;
    document.getElementById("repDeactivated").textContent = deactivated;
    document.getElementById("repLogins").textContent = todayCount;
    document.getElementById("repPending").textContent = pendingCount;
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

/* ANNOUNCEMENTS — live, shared across all portals */

function sendAnnouncement(){
    const text = document.getElementById("announcementText").value.trim();
    if(!text){
        alert("Write something before sending.");
        return;
    }

    const formData = new FormData();
    formData.append('message', text);

    fetch('send_announcement.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                document.getElementById("announcementText").value = "";
                loadAnnouncements();
            } else {
                alert(data.message || "Could not send announcement.");
            }
        })
        .catch(() => alert("Could not send announcement. Please try again."));
}

function loadAnnouncements(){
    fetch('get_announcements.php')
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById("announcementHistory");
            if(!data.success || !data.announcements.length){
                el.innerHTML = '<p style="font-size:12px;color:var(--text-muted);margin-top:10px;">No announcements sent yet.</p>';
                return;
            }
            el.innerHTML = data.announcements.map(a => `
                <p style="font-size:12px;color:var(--text-muted);margin-top:10px;">
                    <strong>${a.sender_name}</strong> (${a.sender_role}) — ${new Date(a.created_at).toLocaleString()}<br>
                    "${a.message}"
                </p>
            `).join('');
        })
        .catch(() => {
            document.getElementById("announcementHistory").innerHTML =
                '<p style="font-size:12px;color:var(--text-muted);margin-top:10px;">Could not load announcements.</p>';
        });
}

/*CONTACT MESSAGES — from the public Contact page, also visible to the Dean*/

function loadContactMessages(){
    fetch('get_contact_messages.php')
        .then(res => res.json())
        .then(data => {
            const body = document.getElementById("contactMessagesBody");
            if(!data.success || !data.messages.length){
                body.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No messages yet.</td></tr>';
                return;
            }
            body.innerHTML = data.messages.map(m => `
                <tr>
                    <td>${m.full_name}</td>
                    <td>${m.email}</td>
                    <td>${m.subject}</td>
                    <td style="max-width:280px;white-space:normal;word-break:break-word;">${m.message}</td>
                    <td class="time">${new Date(m.created_at).toLocaleString()}</td>
                </tr>
            `).join('');
        })
        .catch(() => {
            document.getElementById("contactMessagesBody").innerHTML =
                '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Could not load messages.</td></tr>';
        });
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

/*PROFILE PICTURE*/

function uploadAvatar(input){
    const file = input.files[0];
    if(!file) return;

    const formData = new FormData();
    formData.append('avatar', file);

    fetch('upload_avatar.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                document.getElementById('profileAvatarPreview').src = data.avatar + '?t=' + Date.now();
            } else {
                alert(data.message || 'Could not upload the image.');
            }
        })
        .catch(() => alert('Could not upload the image. Please try again.'));
}

/*INIT */

loadUsers(); // populates the Users tab + dashboard stats with real DB counts
loadLoginActivity();
loadMyResources();
loadCounselorRequests(); // populates statPending + assignments tab even before it's opened
loadAnnouncements();