<?php
session_start();

// Server-side auth gate
if (!isset($_SESSION['cLoggedIn']) || $_SESSION['cLoggedIn'] !== true) {
    header('Location: counselor-login.html');
    exit;
}

$counselorTitle  = htmlspecialchars($_SESSION['cTitle'] ?? '');
$counselorName   = htmlspecialchars($_SESSION['cName'] ?? 'Counselor');
$counselorEmail  = htmlspecialchars($_SESSION['cEmail'] ?? '');
$counselorDept   = htmlspecialchars($_SESSION['cDept'] ?? '');
$counselorAvatar = htmlspecialchars($_SESSION['cAvatar'] ?? 'https://i.pravatar.cc/80?img=33');
$counselorId     = htmlspecialchars($_SESSION['cId'] ?? '');
$fullTitleName   = trim($counselorTitle . ' ' . $counselorName);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Counselor Portal | CampusCare</title>
<link rel="icon" type="image/svg+xml" href="favicon.svg">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="counselorportal.css">
</head>
<body>


<!--TOP NAV-->
<nav class="c-nav">
    <a href="Home.html" class="c-nav-logo">
        <i class="fa-solid fa-stethoscope"></i>
        <div>
            <h2>CampusCare</h2>
            <p>Counselor Portal</p>
        </div>
    </a>

    <ul class="c-nav-tabs">
        <li><button class="active" onclick="switchTab('dashboard', this)">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </button></li>
        <li><button onclick="switchTab('requests', this)">
            <i class="fa-solid fa-inbox"></i> Requests
            <span class="badge" id="reqBadge">3</span>
        </button></li>
        <li><button onclick="switchTab('students', this)">
            <i class="fa-solid fa-users"></i> My Students
        </button></li>
        <li><button onclick="switchTab('academics', this)">
            <i class="fa-solid fa-graduation-cap"></i> Academics
        </button></li>
        <li><button onclick="switchTab('sessions', this)">
            <i class="fa-solid fa-calendar-check"></i> Sessions
        </button></li>
        <li><button onclick="switchTab('trends', this)">
            <i class="fa-solid fa-chart-line"></i> Trends
        </button></li>
        <li><button onclick="switchTab('notes', this)">
            <i class="fa-solid fa-notes-medical"></i> Notes
        </button></li>
        <li><button onclick="switchTab('announcements', this)">
            <i class="fa-solid fa-bullhorn"></i> Announcements
        </button></li>
        <li><button onclick="switchTab('postresource', this)">
            <i class="fa-solid fa-link"></i> Post Resource
        </button></li>
        <li><button onclick="switchTab('profile', this)">
            <i class="fa-solid fa-id-badge"></i> Profile
        </button></li>
    </ul>

    <div class="c-nav-right">
        <button class="logout-btn" onclick="toggleTheme()" title="Toggle dark / light mode">
            <i class="fa-solid fa-moon theme-toggle-icon"></i>
        </button>
        <a href="Home.html" class="home-link">
            <i class="fa-solid fa-arrow-left"></i> Main Site
        </a>
        <button class="logout-btn" onclick="signOut()">
            <i class="fa-solid fa-right-from-bracket"></i> Log Out
        </button>
        <div class="c-avatar-btn" onclick="switchTab('profile', null)">
            <img id="navAvatar" src="<?= $counselorAvatar ?>" alt="Profile">
        </div>
    </div>
</nav>


<!--DASHBOARD-->
<div id="dashboard" class="c-page active">

    <div class="c-hero">
        <div class="c-hero-inner">
            <div class="c-hero-profile">
                <img id="heroAvatar" class="c-hero-avatar" src="<?= $counselorAvatar ?>" alt="Counselor">
                <div class="c-hero-text">
                    <p class="eyebrow">Counselor Portal</p>
                    <h1 id="heroName">Welcome, <?= $fullTitleName ?></h1>
                    <p class="hero-sub" id="heroSub">Loading your dashboard…</p>
                </div>
            </div>
            <div class="c-stat-pills">
                <div class="stat-pill">
                    <div class="s-num" id="statStudents">0</div>
                    <div class="s-label">Students</div>
                </div>
                <div class="stat-pill">
                    <div class="s-num" id="statSessions">0</div>
                    <div class="s-label">Sessions Today</div>
                </div>
                <div class="stat-pill" style="border-color:rgba(239,68,68,0.4);">
                    <div class="s-num" style="color:#fca5a5;" id="statAlerts">3</div>
                    <div class="s-label" style="color:#fca5a5;">High Risk</div>
                </div>
            </div>
        </div>
    </div>

    <div class="c-body">
        <!-- Pending requests snapshot -->
        <div class="section-header">
            <h2><i class="fa-solid fa-inbox"></i> Pending Requests</h2>
            <button class="btn btn-outline btn-sm" onclick="switchTab('requests', null)">
                View all <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        <div class="c-card" style="margin-bottom:24px;">
            <div class="request-list" id="dashRequestList"></div>
        </div>

        <!-- High risk students snapshot -->
        <div class="section-header">
            <h2><i class="fa-solid fa-triangle-exclamation"></i> High Risk Students</h2>
            <button class="btn btn-outline btn-sm" onclick="switchTab('students', null)">
                All students <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        <div class="c-card">
            <div class="student-list" id="dashRiskList"></div>
        </div>
    </div>
</div>


<!--REQUESTS-->
<div id="requests" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Inbox</p>
        <h2>Student Requests</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div class="c-card">
            <div class="request-list" id="fullRequestList"></div>
        </div>
    </div>
</div>


<!--MY STUDENTS-->
<div id="students" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Assigned</p>
        <h2>My Students</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div class="c-card">
            <div class="student-list" id="fullStudentList"></div>
        </div>
    </div>
</div>


<!-- ══════════ ACADEMIC OVERVIEW ══════════ -->
<div id="academics" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Academic Standing</p>
        <h2>Academic Overview</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div class="c-card">
            <div style="overflow-x:auto;">
                <table class="c-table" id="academicOverviewTable">
                    <thead>
                        <tr>
                            <th>Admission No</th>
                            <th>Name</th>
                            <th>Avg Score</th>
                            <th>Failing Units</th>
                            <th>Absence %</th>
                            <th>Flag</th>
                        </tr>
                    </thead>
                    <tbody id="academicOverviewBody">
                        <tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">Loading academic data…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!--SESSIONS-->
<div id="sessions" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Attendance</p>
        <h2>Counseling Sessions</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div class="c-card">
            <div style="overflow-x:auto;">
                <table class="c-table" id="sessionsTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Attendance</th>
                            <th>Mark</th>
                        </tr>
                    </thead>
                    <tbody id="sessionsBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- WELLNESS TRENDS -->
<div id="trends" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Analytics</p>
        <h2>Wellness Trends</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div class="trend-grid" id="trendGrid"></div>
    </div>
</div>


<!-- NOTES -->
<div id="notes" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Confidential</p>
        <h2>Session Notes</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <div class="c-card">
                <h3><i class="fa-solid fa-pen-to-square"></i> New Note</h3>
                <div class="notes-block">
                    <div class="note-student-select">
                        <label style="font-size:13px;font-weight:600;color:#64748b;">Student:</label>
                        <select class="c-select" id="noteStudent">
                            <option value="">— Select student —</option>
                        </select>
                    </div>
                    <textarea class="c-textarea" id="noteText"
                        placeholder="Write confidential counseling notes here. Only you can see these."></textarea>
                    <button class="btn btn-teal" onclick="saveNote()">
                        <i class="fa-solid fa-floppy-disk"></i> Save Note
                    </button>
                </div>
            </div>

            <div class="c-card">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Notes</h3>
                <div class="saved-notes" id="savedNotesList">
                    <p style="font-size:13px;color:#aaa;">No notes saved yet.</p>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- ANNOUNCEMENTS -->
<div id="announcements" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Platform-wide</p>
        <h2>Announcements</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div class="c-card">
            <div id="announcementHistory">
                <p style="font-size:13px;color:#aaa;">Loading announcements…</p>
            </div>
        </div>
    </div>
</div>


<!-- POST RESOURCE -->
<div id="postresource" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Resource Library</p>
        <h2>Post a Resource</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <div class="c-card">
                <h3><i class="fa-solid fa-link"></i> Share a Link</h3>
                <div class="notes-block">
                    <label style="font-size:13px;font-weight:600;color:#64748b;">Title</label>
                    <input class="c-input" id="resourceTitle" type="text" placeholder="e.g. 5-Minute Breathing Exercise">

                    <label style="font-size:13px;font-weight:600;color:#64748b;margin-top:10px;display:block;">Type</label>
                    <select class="c-select" id="resourceType">
                        <option value="Video">Video (e.g. YouTube)</option>
                        <option value="Article">Article</option>
                        <option value="Worksheet">Worksheet</option>
                        <option value="Audio">Audio</option>
                        <option value="Link">Other Link</option>
                    </select>

                    <label style="font-size:13px;font-weight:600;color:#64748b;margin-top:10px;display:block;">Link (URL)</label>
                    <input class="c-input" id="resourceUrl" type="url" placeholder="https://youtube.com/watch?v=...">

                    <label style="font-size:13px;font-weight:600;color:#64748b;margin-top:10px;display:block;">Description (optional)</label>
                    <textarea class="c-textarea" id="resourceDescription" placeholder="What is this resource about, and who is it for?"></textarea>

                    <button class="btn btn-teal" onclick="postResource()" style="margin-top:10px;">
                        <i class="fa-solid fa-plus"></i> Post Resource
                    </button>
                </div>
            </div>

            <div class="c-card">
                <h3><i class="fa-solid fa-book-open"></i> Resources I've Posted</h3>
                <div id="myResourcesList">
                    <p style="font-size:13px;color:#aaa;">Loading…</p>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- PROFILE -->
<div id="profile" class="c-page">
    <div class="c-page-header">
        <p class="eyebrow">Your Account</p>
        <h2>Profile Settings</h2>
    </div>
    <div class="c-body" style="margin-top:0; padding-top:0;">
        <div class="c-card">
            <div class="avatar-upload-row" style="margin-bottom:24px;">
                <img id="profileAvatarPreview" class="c-avatar-preview" src="<?= $counselorAvatar ?>" alt="Profile">
                <label class="upload-label" for="profileUploadInput">
                    <i class="fa-solid fa-camera"></i> Change photo
                </label>
                <input type="file" id="profileUploadInput" accept="image/*" onchange="handleAvatarUpload(this)">
            </div>

            <div class="profile-form">
                <div class="form-group">
                    <label for="pfTitle">Title</label>
                    <select class="c-input" id="pfTitle">
                        <option <?= $counselorTitle === 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                        <option <?= $counselorTitle === 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                        <option <?= $counselorTitle === 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                        <option <?= $counselorTitle === 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                        <option <?= $counselorTitle === 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pfName">Full Name</label>
                    <input class="c-input" type="text" id="pfName" placeholder="First and last name" value="<?= $counselorName ?>">
                </div>
                <div class="form-group">
                    <label for="pfEmail">Email</label>
                    <input class="c-input" type="email" id="pfEmail" placeholder="staff@university.ac.ke" value="<?= $counselorEmail ?>">
                </div>
                <div class="form-group">
                    <label for="pfDept">Department</label>
                    <input class="c-input" type="text" id="pfDept" placeholder="Student Wellness Centre" value="<?= $counselorDept ?>">
                </div>
                <div class="form-group full">
                    <label>Availability</label>
                    <div class="avail-row">
                        <button class="avail-opt selected-avail" onclick="setAvail(this,'Available')">
                            <i class="fa-solid fa-circle" style="color:#10b981;font-size:9px;"></i> Available
                        </button>
                        <button class="avail-opt" onclick="setAvail(this,'Busy')">
                            <i class="fa-solid fa-circle" style="color:#f59e0b;font-size:9px;"></i> Busy
                        </button>
                        <button class="avail-opt" onclick="setAvail(this,'Away')">
                            <i class="fa-solid fa-circle" style="color:#94a3b8;font-size:9px;"></i> Away
                        </button>
                    </div>
                </div>
            </div>

            <button class="btn btn-teal" style="margin-top:24px;" onclick="saveProfile()">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </div>
    </div>
</div>


<!-- TOAST -->
<div class="toast" id="toast">
    <i class="fa-solid fa-circle-check"></i>
    <span id="toastMsg">Saved</span>
</div>


<script>
let REQUESTS = []; // populated by loadAssignedRequests()
let ACADEMIC_DATA = []; // populated by loadStudentAcademics()
let ASSIGNED_STUDENTS = []; // merge of accepted requests + academic data, rebuilt by buildAssignedStudents()
let SESSIONS = []; // populated by loadSessions(), backed by the real `sessions` table

/* LOG OUT */
function signOut() {
    fetch('logout.php').finally(() => {
        window.location.href = 'counselor-login.html';
    });
}

/* INIT PORTAL */
function initPortal() {
    loadSessions();
    populateNoteSelect();
    loadSavedNotes();
    loadStudentAcademics();      // fetches ACADEMIC_DATA, then calls buildAssignedStudents()
    loadAssignedRequests();      // fetches REQUESTS, then calls buildAssignedStudents()
}

/* Combine accepted requests (names/avatars) with academic data (scores/risk flags)
   into one list of students actually assigned to this counselor. */
function buildAssignedStudents() {
    const accepted = REQUESTS.filter(r => r.status === 'accepted');

    ASSIGNED_STUDENTS = accepted.map(r => {
        const academic = ACADEMIC_DATA.find(a => a.admission_no === r.admission_no);
        const flag = academic ? academic.academic_flag : 'Normal';
        const risk = flag === 'At Risk' ? 'critical' : flag === 'Monitor' ? 'support' : 'stable';
        return {
            id: r.admission_no,
            name: r.student_name,
            avatar: r.student_avatar || 'https://i.pravatar.cc/80?img=33',
            score: academic && academic.avg_score !== null ? Math.round(academic.avg_score) : 0,
            risk
        };
    });

    renderDashRisk();
    renderFullStudents();
    renderTrends();
    populateNoteSelect();
}

/* TAB SWITCHING */
function switchTab(id, btn) {
    document.querySelectorAll('.c-page').forEach(p => p.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    if (btn) {
        document.querySelectorAll('.c-nav-tabs button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
    window.scrollTo(0, 0);
    if (id === 'announcements') loadAnnouncements();
    if (id === 'postresource') loadMyResources();
}

/* ANNOUNCEMENTS — read-only, sent by admin/dean */
function loadAnnouncements() {
    fetch('get_announcements.php')
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById('announcementHistory');
            if (!data.success || !data.announcements.length) {
                el.innerHTML = '<p style="font-size:13px;color:#aaa;">No announcements yet.</p>';
                return;
            }
            el.innerHTML = data.announcements.map(a => `
                <div class="note-entry">
                    <div class="note-meta">
                        <span>${a.sender_name} (${a.sender_role})</span>
                        <span>${new Date(a.created_at).toLocaleString()}</span>
                    </div>
                    <p>${a.message}</p>
                </div>
            `).join('');
        })
        .catch(() => {
            document.getElementById('announcementHistory').innerHTML =
                '<p style="font-size:13px;color:#aaa;">Could not load announcements.</p>';
        });
}

/* POST RESOURCE — links to videos, articles, etc. shared with students */
function postResource() {
    const title       = document.getElementById('resourceTitle').value.trim();
    const type        = document.getElementById('resourceType').value;
    const url         = document.getElementById('resourceUrl').value.trim();
    const description = document.getElementById('resourceDescription').value.trim();

    if (!title || !url) {
        showToast('Add a title and a link first.');
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
            showToast(data.message || 'Resource posted.');
            if (data.success) {
                document.getElementById('resourceTitle').value = '';
                document.getElementById('resourceUrl').value = '';
                document.getElementById('resourceDescription').value = '';
                loadMyResources();
            }
        })
        .catch(() => showToast('Could not post the resource. Please try again.'));
}

function loadMyResources() {
    fetch('get_my_resources.php')
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById('myResourcesList');
            if (!data.success || !data.resources.length) {
                el.innerHTML = '<p style="font-size:13px;color:#aaa;">No resources posted yet.</p>';
                return;
            }
            const typeIcon = { Video: 'fa-video', Article: 'fa-newspaper', Worksheet: 'fa-file-pen', Audio: 'fa-headphones', Link: 'fa-link' };
            el.innerHTML = data.resources.map(r => `
                <div class="note-entry">
                    <div class="note-meta">
                        <span><i class="fa-solid ${typeIcon[r.resource_type] || 'fa-link'}"></i> ${r.resource_type}</span>
                        <span>${new Date(r.created_at).toLocaleDateString()}</span>
                    </div>
                    <p style="font-weight:600;margin-bottom:4px;">${r.title}</p>
                    ${r.description ? `<p style="margin-bottom:6px;">${r.description}</p>` : ''}
                    <a href="${r.url}" target="_blank" rel="noopener" style="font-size:13px;color:#7c3aed;word-break:break-all;">${r.url}</a>
                    <div style="margin-top:8px;">
                        <button class="btn btn-danger btn-sm" onclick="deleteResource(${r.id})">
                            <i class="fa-solid fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            document.getElementById('myResourcesList').innerHTML =
                '<p style="font-size:13px;color:#aaa;">Could not load resources.</p>';
        });
}

function deleteResource(resourceId) {
    if (!confirm('Remove this resource?')) return;

    const formData = new FormData();
    formData.append('resource_id', resourceId);

    fetch('delete_resource.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showToast(data.message || 'Resource removed.');
            if (data.success) loadMyResources();
        })
        .catch(() => showToast('Could not remove the resource.'));
}

/* RENDER HELPERS */
function riskLabel(r) {
    if (r === 'critical') return 'Critical';
    if (r === 'support')  return 'Needs Support';
    return 'Stable';
}

function renderStudentCard(s, actions = '') {
    return `
    <div class="stu-card risk-${s.risk}" id="stucard-${s.id}">
        <img class="stu-avatar" src="${s.avatar}" alt="${s.name}">
        <div class="stu-info">
            <div class="stu-name">${s.name}</div>
            <div class="stu-meta">${s.id}</div>
        </div>
        <div class="stu-score">${s.score}%</div>
        <span class="risk-badge">${riskLabel(s.risk)}</span>
        <div class="stu-actions">${actions}</div>
    </div>`;
}

/* DASHBOARD */
function renderDashRisk() {
    const risk = ASSIGNED_STUDENTS.filter(s => s.risk !== 'stable');
    document.getElementById('dashRiskList').innerHTML = risk.length
        ? risk.map(s => renderStudentCard(s, `<button class="btn btn-teal btn-sm" onclick="switchTab('notes',null)">Add Note</button>`)).join('')
        : '<p style="font-size:13px;color:#aaa;padding:8px 0;">No high-risk students right now.</p>';

    document.getElementById('statStudents').textContent = ASSIGNED_STUDENTS.length;
    document.getElementById('statAlerts').textContent = risk.length;
}

/* REQUESTS — live data */
function loadAssignedRequests() {
    fetch('get_my_assigned_requests.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('dashRequestList').innerHTML =
                    '<p style="font-size:13px;color:#aaa;padding:8px 0;">Could not load requests.</p>';
                document.getElementById('fullRequestList').innerHTML =
                    '<p style="font-size:13px;color:#aaa;padding:8px 0;">Could not load requests.</p>';
                return;
            }

            REQUESTS = data.requests; // [{id, admission_no, message, status, student_name, student_avatar, requested_at, assigned_at, responded_at}]

            const awaitingCount = REQUESTS.filter(r => r.status === 'assigned').length;
            document.getElementById('heroSub').textContent =
                'You have ' + awaitingCount + ' request' + (awaitingCount !== 1 ? 's' : '') + ' awaiting your response.';
            document.getElementById('reqBadge').textContent = awaitingCount;

            renderDashRequests();
            renderFullRequests();
            buildAssignedStudents();
        })
        .catch(() => {
            document.getElementById('dashRequestList').innerHTML =
                '<p style="font-size:13px;color:#aaa;padding:8px 0;">Could not load requests.</p>';
            document.getElementById('fullRequestList').innerHTML =
                '<p style="font-size:13px;color:#aaa;padding:8px 0;">Could not load requests.</p>';
        });
}

function reqCard(r) {
    // Only 'assigned' requests are awaiting THIS counselor's response.
    // 'accepted' means this counselor already said yes — show as accepted.
    const isAwaiting = r.status === 'assigned';
    const isAccepted = r.status === 'accepted';
    const when = r.assigned_at || r.requested_at;

    return `
    <div class="request-card ${isAwaiting ? 'new-request' : ''}" id="reqcard-${r.id}">
        <img class="stu-avatar" src="${r.student_avatar || 'https://i.pravatar.cc/80?img=1'}" alt="${r.student_name}" style="width:44px;height:44px;">
        <div class="req-info">
            <div class="req-name">${r.student_name} <span style="font-size:11px;font-weight:400;color:#aaa;">${r.admission_no}</span></div>
            <div class="req-meta">${when ? new Date(when).toLocaleString('en-GB', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' }) : ''} · ${r.message || 'No message provided.'}</div>
        </div>
        ${isAwaiting ? `
        <div class="req-actions">
            <button class="btn btn-green btn-sm" onclick="respondToRequest(${r.id}, 'accept')">
                <i class="fa-solid fa-check"></i> Accept
            </button>
            <button class="btn btn-danger btn-sm" onclick="respondToRequest(${r.id}, 'decline')">
                <i class="fa-solid fa-xmark"></i> Decline
            </button>
        </div>` : isAccepted ? `<span class="att-badge att-attended">Accepted</span>` : ''}
    </div>`;
}

function renderDashRequests() {
    const awaiting = REQUESTS.filter(r => r.status === 'assigned').slice(0, 3);
    const el = document.getElementById('dashRequestList');
    if (!awaiting.length) { el.innerHTML = '<p style="font-size:13px;color:#aaa;padding:8px 0;">No requests awaiting your response.</p>'; return; }
    el.innerHTML = awaiting.map(r => reqCard(r)).join('');
}

function renderFullRequests() {
    const el = document.getElementById('fullRequestList');
    if (!REQUESTS.length) { el.innerHTML = '<p style="font-size:13px;color:#aaa;padding:8px 0;">No requests assigned to you yet.</p>'; return; }
    el.innerHTML = REQUESTS.map(r => reqCard(r)).join('');
}

function respondToRequest(requestId, action) {
    const formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('action', action);

    fetch('respond_request.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showToast(data.message || (action === 'accept' ? 'Request accepted.' : 'Request declined.'));
            loadAssignedRequests();
        })
        .catch(() => showToast('Could not update the request. Please try again.'));
}

/* STUDENTS */
function renderFullStudents() {
    document.getElementById('fullStudentList').innerHTML = ASSIGNED_STUDENTS.length
        ? ASSIGNED_STUDENTS.map(s =>
            renderStudentCard(s, `
                <button class="btn btn-outline btn-sm" onclick="switchTab('notes',null)">
                    <i class="fa-solid fa-notes-medical"></i> Note
                </button>
                <button class="btn btn-teal btn-sm" onclick="switchTab('trends',null)">
                    <i class="fa-solid fa-chart-line"></i> Trend
                </button>
                <button class="btn btn-outline btn-sm" onclick="referStudent('${s.id}', '${s.name.replace(/'/g, "\\'")}', '${s.risk}')">
                    <i class="fa-solid fa-people-arrows"></i> Refer to Dean
                </button>
            `)
        ).join('')
        : '<p style="font-size:13px;color:#aaa;padding:8px 0;">No students assigned to you yet — accepted requests will appear here.</p>';
}

/* REFER TO DEAN */
function referStudent(studentId, studentName, currentRisk) {
    const riskMap = { critical: 'High', support: 'Medium', stable: 'Low' };
    const suggestedRisk = riskMap[currentRisk] || 'Medium';

    const riskLevel = prompt(`Risk level for ${studentName}? (Low / Medium / High)`, suggestedRisk);
    if (!riskLevel || !['Low', 'Medium', 'High'].includes(riskLevel.trim())) {
        if (riskLevel !== null) showToast('Risk level must be Low, Medium, or High.');
        return;
    }

    const category = prompt(`Issue category for ${studentName}? (e.g. Academic Pressure, Stress & Anxiety)`);
    if (!category || !category.trim()) return;

    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('risk_level', riskLevel.trim());
    formData.append('category', category.trim());

    fetch('refer_student.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => showToast(data.message || 'Referral submitted.'))
        .catch(() => showToast('Could not refer student. Please try again.'));
}

/*SESSIONS — live data from the `sessions` table*/
function loadSessions() {
    fetch('get_my_sessions.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('sessionsBody').innerHTML =
                    '<tr><td colspan="5" style="text-align:center;color:#aaa;padding:20px;">Could not load sessions.</td></tr>';
                return;
            }
            SESSIONS = data.sessions;
            renderSessions();
        })
        .catch(() => {
            document.getElementById('sessionsBody').innerHTML =
                '<tr><td colspan="5" style="text-align:center;color:#aaa;padding:20px;">Could not load sessions.</td></tr>';
        });
}

function renderSessions() {
    const todayStr = new Date().toISOString().slice(0, 10);
    document.getElementById('statSessions').textContent =
        SESSIONS.filter(s => s.session_date === todayStr).length;

    if (!SESSIONS.length) {
        document.getElementById('sessionsBody').innerHTML =
            '<tr><td colspan="5" style="text-align:center;color:#aaa;padding:20px;">No sessions booked yet.</td></tr>';
        return;
    }

    document.getElementById('sessionsBody').innerHTML = SESSIONS.map(s => {
        const attClass = s.attendance === 'attended' ? 'att-attended' : s.attendance === 'missed' ? 'att-missed' : 'att-pending';
        const attLabel = s.attendance === 'attended' ? '<i class="fa-solid fa-circle-check"></i> Attended' :
                         s.attendance === 'missed'   ? '<i class="fa-solid fa-circle-xmark"></i> Missed' :
                         '<i class="fa-solid fa-clock"></i> Upcoming';
        return `<tr>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <img src="${s.student_avatar || 'https://i.pravatar.cc/40?img=33'}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                    <div>
                        <div style="font-weight:600;font-size:13px;">${s.student_name}</div>
                        <div style="font-size:11px;color:#aaa;">${s.student_id}</div>
                    </div>
                </div>
            </td>
            <td>${new Date(s.session_date).toLocaleDateString()}</td>
            <td>${s.session_time || '—'}</td>
            <td><span class="att-badge ${attClass}">${attLabel}</span></td>
            <td>
                <div style="display:flex;gap:6px;">
                    <button class="btn btn-green btn-sm" onclick="markAttendance(${s.id},'attended')">
                        <i class="fa-solid fa-check"></i> Attended
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="markAttendance(${s.id},'missed')">
                        <i class="fa-solid fa-xmark"></i> Missed
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

/* WELLNESS TRENDS */
const trendCharts = {};

function renderTrends() {
    const grid = document.getElementById('trendGrid');

    if (!ASSIGNED_STUDENTS.length) {
        grid.innerHTML = '<p style="font-size:13px;color:#aaa;padding:8px 0;">No students assigned to you yet.</p>';
        return;
    }

    grid.innerHTML = ASSIGNED_STUDENTS.map(s => `
        <div class="trend-card">
            <div class="trend-card-header">
                <img src="${s.avatar}" alt="${s.name}">
                <div>
                    <div class="tc-name">${s.name}</div>
                    <div class="tc-id">${s.id} · <span style="color:${s.risk==='critical'?'#ef4444':s.risk==='support'?'#f59e0b':'#10b981'};font-weight:600;">${riskLabel(s.risk)}</span></div>
                </div>
            </div>
            <div class="trend-chart-wrap">
                <canvas id="chart-${s.id}"></canvas>
            </div>
        </div>
    `).join('');

    setTimeout(() => {
        ASSIGNED_STUDENTS.forEach(s => {
            const ctx = document.getElementById('chart-' + s.id);
            if (!ctx) return;
            if (trendCharts[s.id]) { trendCharts[s.id].destroy(); }
            trendCharts[s.id] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Current avg'],
                    datasets: [{
                        data: [s.score],
                        borderColor: s.risk === 'critical' ? '#ef4444' : s.risk === 'support' ? '#f59e0b' : '#7c3aed',
                        backgroundColor: s.risk === 'critical' ? 'rgba(239,68,68,0.08)' : s.risk === 'support' ? 'rgba(245,158,11,0.08)' : 'rgba(124,58,237,0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        pointRadius: 4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { font: { family: 'Poppins', size: 11 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { ticks: { font: { family: 'Poppins', size: 11 } }, grid: { display: false } }
                    }
                }
            });
        });
    }, 50);
}

/* Academic overview of the assigned students — real data from the DB */
function loadStudentAcademics() {
    fetch('get_counselor_students_academic.php')
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            document.getElementById('academicOverviewBody').innerHTML =
                `<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">${data.message || 'No academic data available.'}</td></tr>`;
            return;
        }

        ACADEMIC_DATA = data.students;
        buildAssignedStudents();

        if (!data.students.length) {
            document.getElementById('academicOverviewBody').innerHTML =
                '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">No students assigned to you yet.</td></tr>';
            return;
        }

        document.getElementById('academicOverviewBody').innerHTML = data.students.map(s => {
            const flagClass = s.academic_flag === 'At Risk' ? 'att-missed' : s.academic_flag === 'Monitor' ? 'att-pending' : 'att-attended';
            return `
            <tr>
                <td>${s.admission_no}</td>
                <td>${s.name}</td>
                <td>${s.avg_score ?? '—'}%</td>
                <td>${s.failing_units} / ${s.total_units}</td>
                <td>${s.overall_absence_pct ?? 0}%</td>
                <td><span class="att-badge ${flagClass}">${s.academic_flag}</span></td>
            </tr>`;
        }).join('');
    })
    .catch(() => {
        document.getElementById('academicOverviewBody').innerHTML =
            '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">Could not load academic data right now.</td></tr>';
    });
}

/* NOTES */
function populateNoteSelect() {
    const sel = document.getElementById('noteStudent');
    sel.innerHTML = '<option value="">— Select student —</option>';
    ASSIGNED_STUDENTS.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.name + ' (' + s.id + ')';
        sel.appendChild(opt);
    });
}

function saveNote() {
    const studentId = document.getElementById('noteStudent').value;
    const text      = document.getElementById('noteText').value.trim();
    if (!studentId || !text) { showToast('Select a student and write a note first.'); return; }

    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('note', text);

    fetch('save_counselor_note.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showToast(data.message || 'Note saved.');
            if (data.success) {
                document.getElementById('noteText').value = '';
                document.getElementById('noteStudent').value = '';
                loadSavedNotes();
            }
        })
        .catch(() => showToast('Could not save note. Please try again.'));
}

function loadSavedNotes() {
    fetch('get_counselor_notes.php')
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById('savedNotesList');
            if (!data.success || !data.notes.length) {
                el.innerHTML = '<p style="font-size:13px;color:#aaa;">No notes saved yet.</p>';
                return;
            }
            el.innerHTML = data.notes.slice(0, 6).map(n => `
                <div class="note-entry">
                    <div class="note-meta">
                        <span><img src="${n.student_avatar || 'https://i.pravatar.cc/40?img=33'}" style="width:18px;height:18px;border-radius:50%;vertical-align:middle;margin-right:5px;">${n.student_name}</span>
                        <span>${new Date(n.created_at).toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })}</span>
                    </div>
                    <p>${n.note}</p>
                </div>
            `).join('');
        })
        .catch(() => {
            document.getElementById('savedNotesList').innerHTML =
                '<p style="font-size:13px;color:#aaa;">Could not load notes.</p>';
        });
}

/* ACTIONS */
function markAttendance(sessionId, status) {
    const formData = new FormData();
    formData.append('session_id', sessionId);
    formData.append('attendance', status);

    fetch('mark_session_attendance.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showToast(data.message || 'Attendance updated.');
            if (data.success) loadSessions();
        })
        .catch(() => showToast('Could not update attendance. Please try again.'));
}

/* PROFILE */
function handleAvatarUpload(input) {
    const file = input.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('avatar', file);

    fetch('upload_avatar.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const src = data.avatar + '?t=' + Date.now();
                ['heroAvatar', 'navAvatar', 'profileAvatarPreview'].forEach(el => {
                    const node = document.getElementById(el);
                    if (node) node.src = src;
                });
                showToast('Profile picture updated.');
            } else {
                showToast(data.message || 'Could not upload the image.');
            }
        })
        .catch(() => showToast('Could not upload the image. Please try again.'));
}

function saveProfile() {
    // Local-only display update for now — wiring this to a real
    // update-profile.php (saving to counselors.full_name/title/etc.) is a good follow-up piece.
    const name  = document.getElementById('pfName').value.trim();
    const title = document.getElementById('pfTitle').value;
    if (name) {
        document.getElementById('heroName').textContent = 'Welcome, ' + title + ' ' + name;
    }
    showToast('Profile updated.');
}

function setAvail(btn, val) {
    document.querySelectorAll('.avail-opt').forEach(b => b.classList.remove('selected-avail'));
    btn.classList.add('selected-avail');
}

/* TOAST */
function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

/* PAGE LOAD — no more sessionStorage auth check needed, PHP already gated the page */
window.onload = function () {
    initPortal();
};
</script>
<script src="theme.js"></script>
</body>
</html>