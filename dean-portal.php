<?php
session_start();

// Server-side auth gate
if (!isset($_SESSION['dLoggedIn']) || $_SESSION['dLoggedIn'] !== true) {
    header('Location: dean-login.html');
    exit;
}

$deanName  = htmlspecialchars($_SESSION['dName'] ?? 'Dean of Students');
$deanEmail = htmlspecialchars($_SESSION['dEmail'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dean of Students Portal | CampusCare</title>

<!-- GOOGLE FONTS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- DEAN PORTAL STYLES -->
<link rel="stylesheet" href="dean-portal.css">
</head>
<body>

<div class="portal">

<!--SIDEBAR -->

<aside class="sidebar" id="sidebar">

    <div class="brand">
        <div class="brand-logo">
            <i class="fa-solid fa-heart"></i>
            <div>
                <h2>CampusCare</h2>
                <span>Mental Health Support</span>
            </div>
        </div>

        <div class="dean-chip">
            <div class="dean-avatar"><?= strtoupper(substr($deanName, 0, 1) . (strpos($deanName, ' ') !== false ? substr($deanName, strpos($deanName, ' ') + 1, 1) : '')) ?></div>
            <div class="dean-info">
                <h4><?= $deanName ?></h4>
                <span>Dean of Students</span>
            </div>
        </div>
    </div>

    <nav>
        <p class="nav-group-label">Overview</p>
        <ul>
            <li class="active" onclick="showSection('dashboard', this)">
                <i class="fa-solid fa-gauge-high"></i>
                Dashboard
                <span class="badge-pill" id="alertBadge">3</span>
            </li>
        </ul>

        <p class="nav-group-label">Students</p>
        <ul>
            <li onclick="showSection('students', this)">
                <i class="fa-solid fa-user-graduate"></i>
                Referred Students
            </li>
            <li onclick="showSection('cases', this)">
                <i class="fa-solid fa-folder-open"></i>
                Case Tracking
            </li>
            <li onclick="showSection('reminders', this)">
                <i class="fa-solid fa-bell"></i>
                Follow-up Reminders
            </li>
        </ul>

        <p class="nav-group-label">Insights</p>
        <ul>
            <li onclick="showSection('trends', this)">
                <i class="fa-solid fa-chart-line"></i>
                Reports &amp; Trends
            </li>
            <li onclick="showSection('resources', this)">
                <i class="fa-solid fa-book-open"></i>
                Post Resources
            </li>
        </ul>

        <p class="nav-group-label">Communication</p>
        <ul>
            <li onclick="showSection('announcements', this)">
                <i class="fa-solid fa-bullhorn"></i>
                Announcements
            </li>
            <li onclick="showSection('messages', this)">
                <i class="fa-solid fa-envelope-open-text"></i>
                Contact Messages
            </li>
        </ul>

        <p class="nav-group-label">Account</p>
        <ul>
            <li onclick="showSection('profile', this)">
                <i class="fa-solid fa-gear"></i>
                Profile Settings
            </li>
        </ul>
    </nav>

    <div class="logout-btn" onclick="logout()">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        Logout
    </div>

</aside>

<!-- MAIN -->

<div class="main">

    <!-- TOPBAR -->
    <header class="topbar">
        <div style="display:flex; align-items:center; gap:14px;">
            <button class="nav-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="topbar-left">
                <h1 id="pageTitle">Dashboard</h1>
                <p id="pageSub">Welfare overview — University of CampusCare</p>
            </div>
        </div>
        <div class="topbar-right">
            <span id="liveClock"></span>
            <div class="notification-bell" onclick="toggleTheme()" title="Toggle dark / light mode">
                <i class="fa-solid fa-moon theme-toggle-icon"></i>
            </div>
            <div class="notification-bell" onclick="showSection('dashboard', document.querySelector('.sidebar li'))">
                <i class="fa-solid fa-bell"></i>
                <span class="bell-dot"></span>
            </div>
        </div>
    </header>

    <!-- CONTENT AREA -->
    <div class="content-wrap">

        <!-- DASHBOARD -->
        <section id="dashboard" class="content active">

            <!-- WELCOME BANNER -->
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Welcome, <?= $deanName ?> 👋</h2>
                    <p>Here is today's snapshot of student welfare across the university.<br>
                    You have <strong id="alertCount">3</strong> new alerts requiring your attention.</p>
                </div>
                <div class="welcome-date" id="todayLabel"></div>
            </div>

            <!-- STAT CARDS -->
            <div class="stat-grid" id="dashStatGrid"></div>

            <!-- CHARTS + ALERTS ROW -->
            <div class="two-col">

                <div class="card">
                    <div class="card-head">
                        <h3>New Cases by Week</h3>
                    </div>
                    <canvas id="weeklyChart"></canvas>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h3>New Alerts</h3>
                    </div>
                    <div id="alertsFeed"></div>
                </div>

            </div>

            <!-- RECENT REFERRALS + REMINDERS -->
            <div class="two-col">

                <div class="card">
                    <div class="card-head">
                        <h3>Recently Referred Students</h3>
                        <button class="btn btn-ghost btn-sm" onclick="showSection('students', document.querySelectorAll('.sidebar li')[2])">
                            View all <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr><th>Name</th><th>Risk</th><th>Status</th><th>Referred</th></tr>
                            </thead>
                            <tbody id="recentReferralsBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h3>Upcoming Follow-ups</h3>
                        <button class="btn btn-ghost btn-sm" onclick="showSection('reminders', document.querySelectorAll('.sidebar li')[4])">
                            View all <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="reminder-list" id="dashReminders"></div>
                </div>

            </div>

        </section>

        <!-- REFERRED STUDENTS -->
        <section id="students" class="content">

            <div class="privacy-notice">
                <i class="fa-solid fa-shield-halved"></i>
                <div>
                    <strong>Privacy protected view.</strong> You can see student names, risk levels, referral status,
                    and case progress. Detailed therapy notes, private counselor records, and confidential
                    psychiatric information are not accessible at this level.
                </div>
            </div>

            <div class="card">
                <div class="toolbar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="studentSearch" placeholder="Search by name or ID" oninput="renderStudents()">
                    </div>
                    <div class="filter-row">
                        <button class="filter-btn active-filter" data-filter="all" onclick="setFilter('all', this)">All</button>
                        <button class="filter-btn" data-filter="High" onclick="setFilter('High', this)">High Risk</button>
                        <button class="filter-btn" data-filter="Medium" onclick="setFilter('Medium', this)">Medium Risk</button>
                        <button class="filter-btn" data-filter="Low" onclick="setFilter('Low', this)">Low Risk</button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Risk Level</th>
                                <th>Category</th>
                                <th>Case Status</th>
                                <th>Assigned Counselor</th>
                                <th>Referred On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody"></tbody>
                    </table>
                </div>
            </div>

        </section>

        <!-- CASE TRACKING -->
        <section id="cases" class="content">

            <div class="card">
                <div class="card-head">
                    <h3>Case Status Tracker</h3>
                    <div class="filter-row">
                        <button class="filter-btn active-filter" data-cf="all" onclick="setCaseFilter('all', this)">All</button>
                        <button class="filter-btn" data-cf="Open" onclick="setCaseFilter('Open', this)">Open</button>
                        <button class="filter-btn" data-cf="In Progress" onclick="setCaseFilter('In Progress', this)">In Progress</button>
                        <button class="filter-btn" data-cf="Follow-Up" onclick="setCaseFilter('Follow-Up', this)">Follow-Up</button>
                        <button class="filter-btn" data-cf="Resolved" onclick="setCaseFilter('Resolved', this)">Resolved</button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Case ID</th>
                                <th>Student</th>
                                <th>Risk</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody id="casesTableBody"></tbody>
                    </table>
                </div>
            </div>

        </section>

        <!-- FOLLOW-UP REMINDERS -->
        <section id="reminders" class="content">

            <div class="card" style="max-width:520px;">
                <h3 style="margin-bottom:16px;">Add a Follow-up Reminder</h3>

                <label>Student</label>
                <select id="reminderStudent"></select>

                <label>Note</label>
                <textarea id="reminderNote" rows="2" placeholder="e.g. Check in after exams"></textarea>

                <label>Due Date</label>
                <input type="date" id="reminderDate">

                <label>Priority</label>
                <select id="reminderPriority">
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                </select>

                <button class="btn btn-primary" style="margin-top:4px;" onclick="addReminder()">
                    <i class="fa-solid fa-plus"></i> Add Reminder
                </button>
            </div>

            <div class="card">
                <div class="card-head">
                    <h3>All Reminders</h3>
                </div>
                <div class="reminder-list" id="allRemindersList"></div>
            </div>

        </section>

        <!-- REPORTS & TRENDS -->
        <section id="trends" class="content">

            <div class="card">
                <div class="card-head">
                    <h3>Generate Report</h3>
                </div>

                <label>Report Type</label>
                <select id="reportType">
                    <option value="risk">Risk Level Distribution</option>
                    <option value="status">Case Status Summary</option>
                    <option value="category">Issue Categories</option>
                    <option value="full">Full Welfare Report</option>
                </select>

                <div style="display:flex; gap:10px; margin-top:6px;">
                    <button class="btn btn-primary" onclick="generateReport()">
                        <i class="fa-solid fa-chart-bar"></i> Generate
                    </button>
                    <button class="btn btn-ghost" onclick="exportCSV()">
                        <i class="fa-solid fa-file-export"></i> Export CSV
                    </button>
                </div>
            </div>

            <div class="card" id="reportOutputCard" style="display:none;">
                <div class="card-head">
                    <h3 id="reportOutputTitle">Report</h3>
                </div>
                <div class="report-stat-grid" id="reportOutputGrid"></div>
            </div>

            <div class="three-col">

                <div class="card">
                    <div class="card-head"><h3>Risk Distribution</h3></div>
                    <canvas id="riskChart"></canvas>
                </div>

                <div class="card">
                    <div class="card-head"><h3>Case Statuses</h3></div>
                    <canvas id="statusChart"></canvas>
                </div>

                <div class="card">
                    <div class="card-head"><h3>Issue Categories</h3></div>
                    <canvas id="categoryChart"></canvas>
                </div>

            </div>

        </section>

        <!-- POST RESOURCES -->
        <section id="resources" class="content">

            <div class="card" style="max-width:600px;">
                <h3 style="margin-bottom:18px;">Post a Resource</h3>

                <label>Resource Title</label>
                <input type="text" id="resTitle" placeholder="e.g. Exam Stress Toolkit">

                <label>Description</label>
                <textarea id="resDesc" rows="3" placeholder="Brief description of what this resource covers..."></textarea>

                <div class="form-grid-2">
                    <div>
                        <label>Category</label>
                        <select id="resCategory">
                            <option>Stress &amp; Anxiety</option>
                            <option>Academic Support</option>
                            <option>Crisis Support</option>
                            <option>Mindfulness</option>
                            <option>General Wellness</option>
                        </select>
                    </div>
                    <div>
                        <label>Audience</label>
                        <select id="resAudience">
                            <option>All Students</option>
                            <option>High Risk Students</option>
                            <option>Counselors</option>
                            <option>Staff</option>
                        </select>
                    </div>
                </div>

                <label>File (PDF, image, document)</label>
                <input type="file" id="resFile">

                <button class="btn btn-primary" style="margin-top:4px;" onclick="postResource()">
                    <i class="fa-solid fa-upload"></i> Post Resource
                </button>
            </div>

            <div class="card">
                <div class="card-head">
                    <h3>Resources I've Posted</h3>
                </div>
                <div id="resourcesList">
                    <p style="color:var(--muted); font-size:13px;">No resources posted yet.</p>
                </div>
            </div>

        </section>

        <!-- ANNOUNCEMENTS -->
        <section id="announcements" class="content">

            <div class="card" style="max-width:600px;">
                <h3 style="margin-bottom:18px;">Send an Announcement</h3>
                <p style="color:var(--muted); font-size:13px; margin-bottom:14px;">Visible to every student and counselor on the platform.</p>

                <textarea id="announcementText" rows="4" placeholder="Write your announcement..."></textarea>

                <button class="btn btn-primary" style="margin-top:14px;" onclick="sendAnnouncement()">
                    <i class="fa-solid fa-bullhorn"></i> Send Announcement
                </button>
            </div>

            <div class="card">
                <div class="card-head">
                    <h3>Announcement History</h3>
                </div>
                <div id="announcementHistory">
                    <p style="color:var(--muted); font-size:13px;">Loading announcements…</p>
                </div>
            </div>

        </section>

        <!-- CONTACT MESSAGES -->
        <section id="messages" class="content">

            <div class="card">
                <div class="card-head">
                    <h3>Messages from the public Contact page</h3>
                </div>
                <p style="color:var(--muted); font-size:13px; margin-bottom:14px;">Also visible to the Admin.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Received</th>
                            </tr>
                        </thead>
                        <tbody id="contactMessagesBody">
                            <tr><td colspan="5" style="text-align:center;color:var(--muted);">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>

        <!-- PROFILE -->
        <section id="profile" class="content">

            <div class="card" style="max-width:460px;">
                <h3 style="margin-bottom:18px;">Profile Settings</h3>

                <label>Full Name</label>
                <input type="text" value="<?= $deanName ?>">

                <label>Title</label>
                <input type="text" value="Dean of Students">

                <label>Email Address</label>
                <input type="email" value="<?= $deanEmail ?>">

                <label>Change Password</label>
                <input type="password" placeholder="••••••••">

                <button class="btn btn-primary" style="margin-top:4px;" onclick="showToast('Profile updated', 'fa-check')">
                    Update Profile
                </button>
            </div>

        </section>

    </div>

</div>
</div>

<!-- STUDENT DETAIL MODAL -->
<div class="modal-overlay" id="studentModal">
    <div class="modal">
        <div class="modal-head">
            <h3 id="modalStudentName">Student Overview</h3>
            <button class="modal-close" onclick="closeModal('studentModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div id="modalBody"></div>

        <div class="privacy-notice" style="margin-top:16px; margin-bottom:0;">
            <i class="fa-solid fa-lock"></i>
            <span>Detailed therapy notes, counselor session records, and private psychiatric information
            are not available at the Dean of Students level.</span>
        </div>

        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('studentModal')">Close</button>
            <button class="btn btn-primary" onclick="closeModal('studentModal'); showSection('cases', document.querySelectorAll('.sidebar li')[3])">
                <i class="fa-solid fa-folder-open"></i> View Case
            </button>
        </div>
    </div>
</div>

<!-- TOAST HOST -->
<div id="toastHost"></div>

<!-- SCRIPT -->
<script>

/*LIVE DATA — populated from the real `referrals` table, see loadReferrals()*/

let students = [];
let alerts = [];
let reminders = [];

const resources = [];

function loadReferrals(){
    fetch('get_dean_referrals.php')
        .then(res => res.json())
        .then(data => {
            students = data.success ? data.students : [];
            renderDashboard();
            renderStudents();
            renderCases();
            populateReminderSelect();
        })
        .catch(() => {
            students = [];
            renderDashboard();
            renderStudents();
            renderCases();
            populateReminderSelect();
        });
}

/*NAVIGATION*/

const pageMeta = {
    dashboard: { title:'Dashboard',           sub:'Welfare overview — University of CampusCare' },
    students:  { title:'Referred Students',   sub:'Students referred for welfare support' },
    cases:     { title:'Case Tracking',       sub:'Track and update student case statuses' },
    reminders: { title:'Follow-up Reminders', sub:'Scheduled check-ins and follow-up tasks' },
    trends:    { title:'Reports & Trends',    sub:'Welfare trends and exported analytics' },
    resources: { title:'Post Resources',      sub:'Publish wellness resources to the resource hub' },
    announcements: { title:'Announcements',   sub:'Message every student and counselor' },
    messages:  { title:'Contact Messages',    sub:'Submissions from the public Contact page' },
    profile:   { title:'Profile Settings',    sub:'Manage your Dean account' }
};

let riskChart, statusChart, categoryChart, weeklyChart;

function showSection(id, el){
    document.querySelectorAll('.content').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');

    document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
    if(el) el.classList.add('active');

    const meta = pageMeta[id];
    if(meta){
        document.getElementById('pageTitle').textContent = meta.title;
        document.getElementById('pageSub').textContent = meta.sub;
    }

    document.getElementById('sidebar').classList.remove('open');

    if(id === 'trends') setTimeout(renderTrendCharts, 50);
    if(id === 'announcements') loadAnnouncements();
    if(id === 'messages') loadContactMessages();
}

function logout(){
    fetch('logout.php').finally(() => {
        window.location.href = 'dean-login.html';
    });
}

/* ANNOUNCEMENTS — live, shared across all portals */

function sendAnnouncement(){
    const text = document.getElementById('announcementText').value.trim();
    if(!text){ showToast('Write something before sending.', 'fa-triangle-exclamation'); return; }

    const formData = new FormData();
    formData.append('message', text);

    fetch('send_announcement.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                document.getElementById('announcementText').value = '';
                loadAnnouncements();
                showToast('Announcement sent.');
            } else {
                showToast(data.message || 'Could not send announcement.', 'fa-triangle-exclamation');
            }
        })
        .catch(() => showToast('Could not send announcement.', 'fa-triangle-exclamation'));
}

function loadAnnouncements(){
    fetch('get_announcements.php')
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById('announcementHistory');
            if(!data.success || !data.announcements.length){
                el.innerHTML = '<p style="color:var(--muted); font-size:13px;">No announcements sent yet.</p>';
                return;
            }
            el.innerHTML = data.announcements.map(a => `
                <p style="font-size:13px;color:var(--muted);margin-bottom:12px;">
                    <strong style="color:var(--text);">${a.sender_name}</strong> (${a.sender_role}) — ${new Date(a.created_at).toLocaleString()}<br>
                    "${a.message}"
                </p>
            `).join('');
        })
        .catch(() => {
            document.getElementById('announcementHistory').innerHTML =
                '<p style="color:var(--muted); font-size:13px;">Could not load announcements.</p>';
        });
}

/* CONTACT MESSAGES — from the public Contact page, also visible to the Admin */
function loadContactMessages(){
    fetch('get_contact_messages.php')
        .then(res => res.json())
        .then(data => {
            const body = document.getElementById('contactMessagesBody');
            if(!data.success || !data.messages.length){
                body.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--muted);">No messages yet.</td></tr>';
                return;
            }
            body.innerHTML = data.messages.map(m => `
                <tr>
                    <td>${m.full_name}</td>
                    <td>${m.email}</td>
                    <td>${m.subject}</td>
                    <td style="max-width:280px;white-space:normal;word-break:break-word;">${m.message}</td>
                    <td class="mono" style="font-size:12px;">${new Date(m.created_at).toLocaleString()}</td>
                </tr>
            `).join('');
        })
        .catch(() => {
            document.getElementById('contactMessagesBody').innerHTML =
                '<tr><td colspan="5" style="text-align:center;color:var(--muted);">Could not load messages.</td></tr>';
        });
}

/*UTILITIES*/

function showToast(msg, icon){
    const host = document.getElementById('toastHost');
    const t = document.createElement('div');
    t.className = 'toast';
    t.innerHTML = `<i class="fa-solid ${icon || 'fa-circle-check'}"></i> ${msg}`;
    host.appendChild(t);
    setTimeout(() => t.remove(), 3200);
}

function closeModal(id){
    document.getElementById(id).classList.remove('open');
}

function riskClass(r){
    return r === 'High' ? 'risk-high' : r === 'Medium' ? 'risk-medium' : 'risk-low';
}

function statusClass(s){
    const map = {
        'Open':'status-open',
        'In Progress':'status-inprogress',
        'Follow-Up':'status-followup',
        'Resolved':'status-resolved',
        'Referred':'status-referred'
    };
    return map[s] || 'status-open';
}

function todayStr(){
    return new Date().toISOString().slice(0,10);
}

/* CLOCK & DATE*/

function tickClock(){
    const now = new Date();
    document.getElementById('liveClock').textContent = now.toLocaleString([], {
        weekday:'short', hour:'2-digit', minute:'2-digit'
    });
}

function setTodayLabel(){
    const now = new Date();
    document.getElementById('todayLabel').textContent = now.toLocaleDateString('en-GB', {
        weekday:'long', day:'numeric', month:'long', year:'numeric'
    });
}

/*DASHBOARD*/

function renderDashboard(){

    const high   = students.filter(s => s.risk === 'High').length;
    const medium = students.filter(s => s.risk === 'Medium').length;
    const low    = students.filter(s => s.risk === 'Low').length;
    const open   = students.filter(s => s.status === 'Open').length;
    const pendingFollowUp = students.filter(s => s.status === 'Follow-Up').length;
    const newAlerts = alerts.slice(0,3).length;

    document.getElementById('alertCount').textContent = newAlerts;
    document.getElementById('alertBadge').textContent = newAlerts;

    const stats = [
        { label:'Students Needing Help', num: students.length, icon:'fa-users', cls:'purple stat-card purple' },
        { label:'High Risk',             num: high,            icon:'fa-triangle-exclamation', cls:'red stat-card red' },
        { label:'Medium Risk',           num: medium,          icon:'fa-circle-exclamation',   cls:'amber stat-card amber' },
        { label:'Low Risk',              num: low,             icon:'fa-circle-check',         cls:'green stat-card green' },
        { label:'Open Cases',            num: open,            icon:'fa-folder-open',          cls:'stat-card purple' },
        { label:'Pending Follow-Ups',    num: pendingFollowUp, icon:'fa-bell',                 cls:'stat-card amber' },
        { label:'New Alerts',            num: newAlerts,       icon:'fa-exclamation-circle',   cls:'stat-card red' }
    ];

    const icoBg = {
        red:'bg-red', amber:'bg-amber', green:'bg-green', purple:'bg-purple'
    };

    document.getElementById('dashStatGrid').innerHTML = stats.map(s => {
        const colour = s.cls.includes('red') ? 'red' : s.cls.includes('amber') ? 'amber' : s.cls.includes('green') ? 'green' : 'purple';
        return `
        <div class="stat-card ${colour}">
            <div class="stat-icon ${icoBg[colour]}"><i class="fa-solid ${s.icon}"></i></div>
            <div class="num">${s.num}</div>
            <div class="label">${s.label}</div>
        </div>`;
    }).join('');

    // Alerts feed
    document.getElementById('alertsFeed').innerHTML = alerts.length
        ? alerts.slice(0,4).map(a => `
            <div class="alert-item">
                <div class="alert-dot ${a.dot}"></div>
                <div class="body">
                    <h4>${a.title}</h4>
                    <p>${a.body}</p>
                    <p class="meta">${a.time}</p>
                </div>
            </div>
        `).join('')
        : '<p style="color:var(--muted); font-size:13px;">No alerts right now.</p>';

    // Recent referrals (last 5)
    document.getElementById('recentReferralsBody').innerHTML = students.length
        ? students.slice(0,5).map(s => `
            <tr>
                <td>${s.name}</td>
                <td><span class="risk ${riskClass(s.risk)}">${s.risk}</span></td>
                <td><span class="status ${statusClass(s.status)}">${s.status}</span></td>
                <td class="mono" style="font-size:12px;">${s.referred}</td>
            </tr>
        `).join('')
        : '<tr class="empty-row"><td colspan="4">No referrals yet.</td></tr>';

    // Upcoming follow-ups in dashboard
    document.getElementById('dashReminders').innerHTML = reminders.length
        ? reminders.slice(0,3).map(r => `
            <div class="reminder-item ${r.priority === 'urgent' ? 'urgent' : ''}">
                <div>
                    <h4>${r.student}</h4>
                    <p>${r.note}</p>
                </div>
                <div class="due">${r.due}</div>
            </div>
        `).join('')
        : '<p style="color:var(--muted); font-size:13px;">No reminders yet.</p>';

    // Weekly chart — last 5 weeks of real referrals, bucketed by referred_at
    if(weeklyChart) weeklyChart.destroy();
    const wCtx = document.getElementById('weeklyChart');
    const weekBuckets = [0,0,0,0,0];
    const oneWeek = 7 * 24 * 60 * 60 * 1000;
    const weekStart = new Date(); weekStart.setHours(0,0,0,0);
    weekStart.setTime(weekStart.getTime() - 4 * oneWeek);
    students.forEach(s => {
        const idx = Math.floor((new Date(s.referred) - weekStart) / oneWeek);
        if(idx >= 0 && idx < 5) weekBuckets[idx]++;
    });
    weeklyChart = new Chart(wCtx, {
        type:'bar',
        data:{
            labels:['Wk 1','Wk 2','Wk 3','Wk 4','Wk 5 (this week)'],
            datasets:[
                { label:'New Referrals', data:weekBuckets, backgroundColor:'#8b5cf6', borderRadius:6 }
            ]
        },
        options:{
            responsive:true,
            plugins:{ legend:{ position:'bottom', labels:{ font:{ size:12 } } } },
            scales:{
                y:{ beginAtZero:true, grid:{ color:'#f3f0fe' } },
                x:{ grid:{ display:false } }
            }
        }
    });
}

/*REFERRED STUDENTS*/

let riskFilter = 'all';

function setFilter(val, btn){
    riskFilter = val;
    document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active-filter'));
    btn.classList.add('active-filter');
    renderStudents();
}

function renderStudents(){
    const term = document.getElementById('studentSearch').value.trim().toLowerCase();

    const filtered = students.filter(s => {
        const matchFilter = riskFilter === 'all' || s.risk === riskFilter;
        const matchSearch = s.name.toLowerCase().includes(term) || s.id.toLowerCase().includes(term);
        return matchFilter && matchSearch;
    });

    const body = document.getElementById('studentsTableBody');

    if(!filtered.length){
        body.innerHTML = `<tr class="empty-row"><td colspan="8">No students match that filter.</td></tr>`;
        return;
    }

    body.innerHTML = filtered.map(s => `
        <tr>
            <td class="mono" style="font-size:12.5px;">${s.id}</td>
            <td style="font-weight:600;">${s.name}</td>
            <td><span class="risk ${riskClass(s.risk)}">${s.risk}</span></td>
            <td>${s.category}</td>
            <td><span class="status ${statusClass(s.status)}">${s.status}</span></td>
            <td>${s.counselor}</td>
            <td class="mono" style="font-size:12px;">${s.referred}</td>
            <td>
                <button class="btn btn-ghost btn-sm" onclick="viewStudent('${s.id}')">
                    <i class="fa-solid fa-eye"></i> View
                </button>
            </td>
        </tr>
    `).join('');
}

function viewStudent(id){
    const s = students.find(x => x.id === id);
    document.getElementById('modalStudentName').textContent = s.name;
    document.getElementById('modalBody').innerHTML = `
        <table style="width:100%; font-size:13.5px; border-collapse:collapse;">
            <tr><td style="padding:8px 0; color:var(--muted); width:45%;">Student ID</td><td class="mono">${s.id}</td></tr>
            <tr><td style="padding:8px 0; color:var(--muted);">Risk Level</td><td><span class="risk ${riskClass(s.risk)}">${s.risk}</span></td></tr>
            <tr><td style="padding:8px 0; color:var(--muted);">Issue Category</td><td>${s.category}</td></tr>
            <tr><td style="padding:8px 0; color:var(--muted);">Case Status</td><td><span class="status ${statusClass(s.status)}">${s.status}</span></td></tr>
            <tr><td style="padding:8px 0; color:var(--muted);">Assigned Counselor</td><td>${s.counselor}</td></tr>
            <tr><td style="padding:8px 0; color:var(--muted);">Referred On</td><td class="mono">${s.referred}</td></tr>
        </table>
    `;
    document.getElementById('studentModal').classList.add('open');
}

/*CASE TRACKING*/

let caseFilter = 'all';

function setCaseFilter(val, btn){
    caseFilter = val;
    document.querySelectorAll('[data-cf]').forEach(b => b.classList.remove('active-filter'));
    btn.classList.add('active-filter');
    renderCases();
}

function renderCases(){
    const filtered = caseFilter === 'all' ? students : students.filter(s => s.status === caseFilter);

    const body = document.getElementById('casesTableBody');

    if(!filtered.length){
        body.innerHTML = `<tr class="empty-row"><td colspan="7">No cases match that filter.</td></tr>`;
        return;
    }

    body.innerHTML = filtered.map(s => `
        <tr>
            <td class="mono" style="font-size:12px;">CASE-${String(s.referral_id).padStart(3,'0')}</td>
            <td style="font-weight:600;">${s.name}</td>
            <td><span class="risk ${riskClass(s.risk)}">${s.risk}</span></td>
            <td>${s.category}</td>
            <td><span class="status ${statusClass(s.status)}">${s.status}</span></td>
            <td class="mono" style="font-size:12px;">${s.referred}</td>
            <td>
                <select style="width:auto; padding:6px 10px; font-size:12.5px; margin-bottom:0;" onchange="updateCaseStatus(${s.referral_id}, this.value)">
                    <option ${s.status==='Open'?'selected':''}>Open</option>
                    <option ${s.status==='In Progress'?'selected':''}>In Progress</option>
                    <option ${s.status==='Follow-Up'?'selected':''}>Follow-Up</option>
                    <option ${s.status==='Resolved'?'selected':''}>Resolved</option>
                </select>
            </td>
        </tr>
    `).join('');
}

function updateCaseStatus(referralId, newStatus){
    const formData = new FormData();
    formData.append('referral_id', referralId);
    formData.append('status', newStatus);

    fetch('update_referral_status.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                const s = students.find(x => x.referral_id === referralId);
                if(s) s.status = newStatus;
                renderCases();
                renderDashboard();
                renderStudents();
                showToast(data.message || 'Case status updated.', 'fa-rotate');
            } else {
                showToast(data.message || 'Could not update status.', 'fa-triangle-exclamation');
                renderCases();
            }
        })
        .catch(() => showToast('Could not update status.', 'fa-triangle-exclamation'));
}

/*FOLLOW-UP REMINDERS*/

function populateReminderSelect(){
    const sel = document.getElementById('reminderStudent');
    // Dedupe by student id — the same student can have more than one referral record
    const seen = new Set();
    const unique = students.filter(s => seen.has(s.id) ? false : seen.add(s.id));
    sel.innerHTML = unique.map(s => `<option value="${s.id}">${s.name} (${s.id})</option>`).join('');
}

function addReminder(){
    const select   = document.getElementById('reminderStudent');
    const studentId = select.value;
    const studentName = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text.replace(/\s*\(.*\)$/, '') : '';
    const note     = document.getElementById('reminderNote').value.trim();
    const due      = document.getElementById('reminderDate').value;
    const priority = document.getElementById('reminderPriority').value;

    if(!studentId || !note || !due){
        showToast('Select a student, add a note, and a due date', 'fa-circle-exclamation');
        return;
    }

    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('note', note);

    fetch('send_reminder.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(!data.success){
                showToast(data.message || 'Could not send reminder.', 'fa-triangle-exclamation');
                return;
            }
            reminders.unshift({ student: studentName, note, due, priority, done:false });
            document.getElementById('reminderNote').value = '';
            document.getElementById('reminderDate').value = '';

            renderAllReminders();
            renderDashboard();
            showToast('Reminder sent to ' + studentName, 'fa-bell');
        })
        .catch(() => showToast('Could not send reminder.', 'fa-triangle-exclamation'));
}

function renderAllReminders(){
    const list = document.getElementById('allRemindersList');

    if(!reminders.length){
        list.innerHTML = '<p style="color:var(--muted); font-size:13px;">No reminders yet.</p>';
        return;
    }

    list.innerHTML = reminders.map((r, i) => `
        <div class="reminder-item ${r.priority === 'urgent' ? 'urgent' : ''} ${r.done ? 'done' : ''}">
            <div>
                <h4>${r.student}</h4>
                <p>${r.note}</p>
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
                <span class="due">${r.due}</span>
                <button class="btn btn-ghost btn-sm" onclick="markDone(${i})">
                    ${r.done ? '<i class="fa-solid fa-rotate-left"></i> Undo' : '<i class="fa-solid fa-check"></i> Done'}
                </button>
            </div>
        </div>
    `).join('');
}

function markDone(idx){
    reminders[idx].done = !reminders[idx].done;
    renderAllReminders();
    renderDashboard();
}

/*RESOURCES*/

function postResource(){
    const title    = document.getElementById('resTitle').value.trim();
    const desc     = document.getElementById('resDesc').value.trim();
    const category = document.getElementById('resCategory').value;
    const audience = document.getElementById('resAudience').value;

    if(!title){
        showToast('Give the resource a title first', 'fa-circle-exclamation');
        return;
    }

    resources.unshift({ title, desc: desc || 'No description.', category, audience, date: todayStr() });

    document.getElementById('resTitle').value = '';
    document.getElementById('resDesc').value = '';
    document.getElementById('resFile').value = '';

    renderResources();
    showToast('Resource posted to the hub', 'fa-upload');
}

function renderResources(){
    const list = document.getElementById('resourcesList');

    if(!resources.length){
        list.innerHTML = '<p style="color:var(--muted); font-size:13px;">No resources posted yet.</p>';
        return;
    }

    list.innerHTML = resources.map((r, i) => `
        <div class="resource-item">
            <div class="resource-icon"><i class="fa-solid fa-book-open"></i></div>
            <div style="flex:1;">
                <h4>${r.title}</h4>
                <p>${r.desc}</p>
                <p class="meta">${r.category} · For ${r.audience} · Posted ${r.date}</p>
            </div>
            <button class="icon-btn" onclick="deleteResource(${i})" title="Remove">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `).join('');
}

function deleteResource(idx){
    if(!confirm(`Remove "${resources[idx].title}"?`)) return;
    resources.splice(idx, 1);
    renderResources();
    showToast('Resource removed', 'fa-trash');
}

/*REPORTS & TRENDS*/

function generateReport(){
    const type = document.getElementById('reportType').value;
    const titleEl = document.getElementById('reportOutputTitle');
    const gridEl  = document.getElementById('reportOutputGrid');
    let stats = [];

    const high     = students.filter(s => s.risk === 'High').length;
    const medium   = students.filter(s => s.risk === 'Medium').length;
    const low      = students.filter(s => s.risk === 'Low').length;
    const open     = students.filter(s => s.status === 'Open').length;
    const inProg   = students.filter(s => s.status === 'In Progress').length;
    const followUp = students.filter(s => s.status === 'Follow-Up').length;
    const resolved = students.filter(s => s.status === 'Resolved').length;

    const cats = {};
    students.forEach(s => { cats[s.category] = (cats[s.category] || 0) + 1; });

    if(type === 'risk'){
        titleEl.textContent = 'Risk Level Distribution';
        stats = [
            {label:'High Risk', num:high},
            {label:'Medium Risk', num:medium},
            {label:'Low Risk', num:low},
            {label:'Total Students', num:students.length}
        ];
    }else if(type === 'status'){
        titleEl.textContent = 'Case Status Summary';
        stats = [
            {label:'Open', num:open},
            {label:'In Progress', num:inProg},
            {label:'Follow-Up', num:followUp},
            {label:'Resolved', num:resolved}
        ];
    }else if(type === 'category'){
        titleEl.textContent = 'Issue Categories';
        stats = Object.entries(cats).map(([k,v]) => ({label:k, num:v}));
    }else{
        titleEl.textContent = 'Full Welfare Report';
        stats = [
            {label:'Total Referred', num:students.length},
            {label:'High Risk', num:high},
            {label:'Medium Risk', num:medium},
            {label:'Low Risk', num:low},
            {label:'Open Cases', num:open},
            {label:'Resolved', num:resolved}
        ];
    }

    gridEl.innerHTML = stats.map(s => `
        <div class="report-stat">
            <div class="num">${s.num}</div>
            <div class="label">${s.label}</div>
        </div>
    `).join('');

    document.getElementById('reportOutputCard').style.display = 'block';
    showToast('Report generated', 'fa-chart-bar');
}

function exportCSV(){
    const rows = [['Student ID','Name','Risk Level','Category','Status','Counselor','Referred On']];
    students.forEach(s => rows.push([s.id, s.name, s.risk, s.category, s.status, s.counselor, s.referred]));

    const csv = rows.map(r => r.map(c => `"${String(c).replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type:'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = `dean-welfare-report-${todayStr()}.csv`;
    document.body.appendChild(a); a.click(); a.remove();
    URL.revokeObjectURL(url);
    showToast('CSV exported', 'fa-file-export');
}

function renderTrendCharts(){

    const high   = students.filter(s => s.risk === 'High').length;
    const medium = students.filter(s => s.risk === 'Medium').length;
    const low    = students.filter(s => s.risk === 'Low').length;

    const open     = students.filter(s => s.status === 'Open').length;
    const inProg   = students.filter(s => s.status === 'In Progress').length;
    const followUp = students.filter(s => s.status === 'Follow-Up').length;
    const resolved = students.filter(s => s.status === 'Resolved').length;

    const cats = {};
    students.forEach(s => { cats[s.category] = (cats[s.category] || 0) + 1; });

    if(riskChart)     riskChart.destroy();
    if(statusChart)   statusChart.destroy();
    if(categoryChart) categoryChart.destroy();

    riskChart = new Chart(document.getElementById('riskChart'), {
        type:'doughnut',
        data:{
            labels:['High','Medium','Low'],
            datasets:[{ data:[high,medium,low], backgroundColor:['#dc2626','#d97706','#059669'], borderWidth:0 }]
        },
        options:{
            cutout:'65%',
            plugins:{ legend:{ position:'bottom', labels:{ font:{size:12} } } }
        }
    });

    statusChart = new Chart(document.getElementById('statusChart'), {
        type:'doughnut',
        data:{
            labels:['Open','In Progress','Follow-Up','Resolved'],
            datasets:[{ data:[open,inProg,followUp,resolved], backgroundColor:['#be123c','#d97706','#7c3aed','#059669'], borderWidth:0 }]
        },
        options:{
            cutout:'65%',
            plugins:{ legend:{ position:'bottom', labels:{ font:{size:12} } } }
        }
    });

    const catLabels = Object.keys(cats);
    const catData   = Object.values(cats);
    const palette   = ['#7c3aed','#8b5cf6','#a78bfa','#c4b5fd','#059669','#d97706'];

    categoryChart = new Chart(document.getElementById('categoryChart'), {
        type:'bar',
        data:{
            labels: catLabels,
            datasets:[{ label:'Students', data:catData, backgroundColor:palette.slice(0,catLabels.length), borderRadius:6 }]
        },
        options:{
            indexAxis:'y',
            plugins:{ legend:{ display:false } },
            scales:{ x:{ beginAtZero:true, grid:{ color:'#f3f0fe' } }, y:{ grid:{ display:false } } }
        }
    });
}

/*MODAL CLOSE ON OVERLAY CLICK*/

document.getElementById('studentModal').addEventListener('click', e => {
    if(e.target.id === 'studentModal') closeModal('studentModal');
});

/*INIT*/

function init(){
    setTodayLabel();
    tickClock();
    setInterval(tickClock, 30000);

    loadReferrals(); // fetches real students/alerts data, then calls renderDashboard/renderStudents/renderCases/populateReminderSelect
    renderAllReminders();
    renderResources();
}

init();

</script>
<script src="theme.js"></script>
</body>
</html>