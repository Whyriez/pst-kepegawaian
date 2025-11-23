// ===== DASHBOARD FUNCTIONALITY =====
function initializeDynamicContent() {
    updateDashboard();
    updateNewFilesBadge();
    
    // Simulate real-time updates
    simulateRealTimeUpdates();
}

function updateDashboard() {
    const newFilesCount = submissionsData.newFiles.length;
    const pendingCount = submissionsData.pending.length;
    const approvedCount = submissionsData.approved.length;
    const rejectedCount = 5; // Static for demo

    // Update stat cards
    updateStatCard('new-files', newFilesCount);
    updateStatCard('pending', pendingCount);
    updateStatCard('approved', approvedCount);
    updateStatCard('rejected', rejectedCount);

    // Update activity table
    updateActivityTable();
    
    // Update alert
    updateNewFilesAlert();
}

function updateStatCard(type, count) {
    const elements = {
        'new-files': { element: '#statNewFiles', alert: true },
        'pending': { element: '#statPending' },
        'approved': { element: '#statApproved' },
        'rejected': { element: '#statRejected' }
    };

    const config = elements[type];
    if (config) {
        const element = document.querySelector(config.element);
        if (element) {
            element.textContent = count;
            
            // Add animation for new files
            if (config.alert && count > 0) {
                element.classList.add('text-pulse');
                setTimeout(() => element.classList.remove('text-pulse'), 2000);
            }
        }
    }
}

function updateActivityTable() {
    const tbody = document.getElementById('activityTableBody');
    if (!tbody) return;

    // Clear existing rows
    tbody.innerHTML = '';

    // Combine all submissions sorted by date
    const allSubmissions = [
        ...submissionsData.newFiles,
        ...submissionsData.pending,
        ...submissionsData.approved
    ].sort((a, b) => new Date(b.date) - new Date(a.date));

    // Add rows
    allSubmissions.forEach(submission => {
        const row = createTableRow(submission);
        tbody.appendChild(row);
    });

    // Update table header badge
    updateTableHeaderBadge();
}

function createTableRow(submission) {
    const row = document.createElement('tr');
    if (submission.status === 'new') {
        row.className = 'table-warning';
    }

    const dateObj = new Date(submission.date);
    const formattedDate = dateObj.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    const formattedTime = dateObj.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });

    const statusBadge = getStatusBadge(submission.status);
    const actionButtons = getActionButtons(submission);

    row.innerHTML = `
        <td>
            <div class="fw-bold">${formattedDate}</div>
            <small class="text-muted">${formattedTime} WIB</small>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <i class="fas ${getSubmissionIcon(submission.type)} text-warning me-2"></i>
                <span>${submission.title}</span>
            </div>
        </td>
        <td>
            <div class="fw-bold">${submission.employee}</div>
            <small class="text-muted">${submission.nip}</small>
        </td>
        <td>${statusBadge}</td>
        <td>${actionButtons}</td>
    `;

    return row;
}

function getSubmissionIcon(type) {
    const icons = {
        'kenaikan-pangkat': 'fa-user-tie',
        'pensiun': 'fa-user-clock',
        'naik-jenjang': 'fa-chart-line'
    };
    return icons[type] || 'fa-file-alt';
}

function getStatusBadge(status) {
    const badges = {
        'new': '<span class="badge bg-warning"><i class="fas fa-circle me-1" style="font-size: 6px;"></i>Baru Masuk</span>',
        'pending': '<span class="badge bg-info"><i class="fas fa-clock me-1" style="font-size: 6px;"></i>Dalam Review</span>',
        'approved': '<span class="badge bg-success"><i class="fas fa-check me-1" style="font-size: 6px;"></i>Disetujui</span>',
        'rejected': '<span class="badge bg-danger"><i class="fas fa-times me-1" style="font-size: 6px;"></i>Ditolak</span>'
    };
    return badges[status] || badges.pending;
}

// ===== FIXED: Get action buttons for dashboard - DISAMAKAN UNTUK SEMUA STATUS =====
function getActionButtons(submission) {
    // Untuk semua status, tampilkan tombol yang sama
    return `
        <div class="d-flex justify-content-center gap-1">
            <button class="btn btn-sm btn-outline-primary px-2" onclick="previewSubmission(${submission.id})" title="Preview" style="height: 28px; width: 36px;">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-success px-2" onclick="acceptFile(${submission.id}, '${submission.employee}')" title="Setujui" style="height: 28px; width: 36px;">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn btn-sm btn-danger px-2" onclick="rejectFile(${submission.id}, '${submission.employee}')" title="Tolak" style="height: 28px; width: 36px;">
                <i class="fas fa-times"></i>
            </button>
            <button class="btn btn-sm btn-warning px-2" onclick="postponeFile(${submission.id}, '${submission.employee}')" title="Tunda" style="height: 28px; width: 36px;">
                <i class="fas fa-pause"></i>
            </button>
        </div>
    `;
}