// Log with colors
function logAction(msg, type = 'info') {
    const log = document.getElementById('activityLog');
    if (!log) return;
    const entry = document.createElement('div');
    entry.className = type === 'error' ? 'text-red-500' : (type === 'success' ? 'text-green-400' : 'text-white');
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
    log.appendChild(entry);
    log.scrollTop = log.scrollHeight;
}

// Fixed Upload Handler
const uploadBtn = document.getElementById('uploadBtn') || document.querySelector('button.bg-green-600');
const fileInput = document.getElementById('assetUploadInput');

if (uploadBtn && fileInput) {
    uploadBtn.onclick = (e) => {
        e.preventDefault();
        logAction("Opening file picker...", 'info');
        fileInput.click();
    };

    fileInput.onchange = async () => {
        const file = fileInput.files[0];
        if (!file) return;
        
        logAction(`Uploading ${file.name}...`, 'info');
        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await fetch('/api/upload.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                logAction("Upload Success!", 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                logAction("Upload Failed: " + data.message, 'error');
            }
        } catch (err) {
            logAction("Network Error during upload", 'error');
        }
    };
}
