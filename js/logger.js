function logAction(msg, type = 'info') {
    const log = document.getElementById('activityLog');
    if (!log) return;
    
    const colors = {
        info: 'text-white',
        success: 'text-green-400',
        error: 'text-red-500'
    };

    const entry = document.createElement('div');
    entry.className = colors[type] || colors.info;
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
    log.appendChild(entry);
    log.scrollTop = log.scrollHeight;
}
