function showStatus(message, isError) {
    const el = document.getElementById('statusMsg');
    el.textContent = message;
    el.className = 'p13-status ' + (isError ? 'p13-status-err' : 'p13-status-ok');
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 3500);
}

function renderCourses(courses) {
    const grid = document.getElementById('courseGrid');
    if (courses.length === 0) {
        grid.innerHTML = '<p class="p13-empty">No courses found.</p>';
        return;
    }
    grid.innerHTML = courses.map(c => `
        <div class="p13-card" data-id="${c.id}">
            <div class="p13-icon-wrap">
                <i class="${escHtml(c.icon)}"></i>
            </div>
            <div class="p13-name">${escHtml(c.name)}</div>
            <div class="p13-desc">${escHtml(c.description || '')}</div>
            ${IS_ADMIN ? `
            <div class="p13-card-btns">
                <button class="p13-btn p13-btn-edit" onclick="startEdit(${c.id}, '${escJs(c.name)}', '${escJs(c.description || '')}', '${escJs(c.icon)}')">Edit</button>
                <button class="p13-btn p13-btn-delete" onclick="deleteCourse(${c.id})">Delete</button>
            </div>` : ''}
        </div>
    `).join('');
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escJs(str) {
    return String(str).replace(/\\/g,'\\\\').replace(/'/g,"\\'");
}

function loadCourses() {
    document.getElementById('courseGrid').innerHTML = '<p id="loadingMsg">Loading courses...</p>';
    fetch(API_URL)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCourses(data.data);
            } else {
                showStatus(data.message || 'Failed to load courses.', true);
                document.getElementById('courseGrid').innerHTML = '<p class="p13-empty">Could not load courses.</p>';
            }
        })
        .catch(() => {
            showStatus('Network error loading courses.', true);
            document.getElementById('courseGrid').innerHTML = '<p class="p13-empty">Could not load courses.</p>';
        });
}

function startEdit(id, name, description, icon) {
    document.getElementById('editId').value       = id;
    document.getElementById('courseName').value   = name;
    document.getElementById('courseDesc').value   = description;
    document.getElementById('courseIcon').value   = icon;
    document.getElementById('formTitle').textContent = 'Edit Course';
    document.getElementById('submitBtn').textContent  = 'Update Course';
    document.getElementById('cancelBtn').style.display = 'inline-block';
    document.getElementById('courseName').focus();
}

function cancelEdit() {
    document.getElementById('editId').value       = '';
    document.getElementById('courseName').value   = '';
    document.getElementById('courseDesc').value   = '';
    document.getElementById('courseIcon').value   = 'fas fa-book';
    document.getElementById('formTitle').textContent = 'Add New Course';
    document.getElementById('submitBtn').textContent  = 'Save Course';
    document.getElementById('cancelBtn').style.display = 'none';
}

function deleteCourse(id) {
    if (!confirm('Delete this course?')) return;
    fetch(API_URL + '?id=' + id, { method: 'DELETE' })
        .then(r => r.json())
        .then(data => {
            showStatus(data.message, !data.success);
            if (data.success) loadCourses();
        })
        .catch(() => showStatus('Network error.', true));
}

if (IS_ADMIN) {
    document.getElementById('cancelBtn').addEventListener('click', cancelEdit);

    document.getElementById('courseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('courseName').value.trim();
        const desc = document.getElementById('courseDesc').value.trim();
        const icon = document.getElementById('courseIcon').value.trim() || 'fas fa-book';
        const id   = document.getElementById('editId').value;

        if (!name) {
            showStatus('Course name is required.', true);
            return;
        }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;

        const payload = JSON.stringify({ name, description: desc, icon });

        if (id) {
            fetch(API_URL + '?id=' + id, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: payload
            })
            .then(r => r.json())
            .then(data => {
                showStatus(data.message, !data.success);
                if (data.success) { cancelEdit(); loadCourses(); }
            })
            .catch(() => showStatus('Network error.', true))
            .finally(() => { btn.disabled = false; });
        } else {
            fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: payload
            })
            .then(r => r.json())
            .then(data => {
                showStatus(data.message, !data.success);
                if (data.success) { cancelEdit(); loadCourses(); }
            })
            .catch(() => showStatus('Network error.', true))
            .finally(() => { btn.disabled = false; });
        }
    });
}

loadCourses();
