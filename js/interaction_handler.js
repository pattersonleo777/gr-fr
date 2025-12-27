let selectedAsset = null;
let tapCount = 0;

function logAction(msg) {
    const log = document.getElementById('activityLog');
    if (!log) return;
    const entry = document.createElement('div');
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
    log.appendChild(entry);
    log.scrollTop = log.scrollHeight;
}

// Fix Upload Trigger
document.getElementById('uploadBtn').onclick = () => {
    logAction("Opening file picker...");
    document.getElementById('assetUploadInput').click();
};

document.getElementById('assetUploadInput').onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    logAction(`Uploading: ${file.name}...`);

    const formData = new FormData();
    formData.append('file', file);
    const res = await fetch('/api/upload.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        logAction("Upload success. Reloading...");
        location.reload();
    } else {
        logAction("ERROR: Upload failed.");
    }
};

window.handleAssetTap = (path, el) => {
    tapCount++;
    setTimeout(() => {
        if (tapCount === 1) {
            document.querySelectorAll('.asset-card').forEach(c => c.style.border = "none");
            el.style.border = "2px solid #ef4444";
            selectedAsset = path;
            logAction(`Selected: ${path.split('/').pop()}`);
        } else if (tapCount === 2) {
            logAction("Opening 3D Inspection Card...");
            open3DCard(path);
        }
        tapCount = 0;
    }, 300);
};

// Canvas Tap to Place with Animation
document.getElementById('rallyView').onclick = (e) => {
    if (selectedAsset) {
        const type = selectedAsset.endsWith('.glb') ? 'Model' : 'Texture';
        logAction(`Placing ${type} onto canvas...`);
        
        selectedAsset.endsWith('.glb') ? loadModelIntoCanvas(selectedAsset) : loadTextureToModel(selectedAsset);
        
        // Visual Success Ripple
        const ripple = document.createElement('div');
        ripple.className = "absolute rounded-full border-2 border-green-500 animate-ping";
        ripple.style.left = `${e.clientX - 25}px`;
        ripple.style.top = `${e.clientY - 25}px`;
        ripple.style.width = "50px";
        ripple.style.height = "50px";
        document.body.appendChild(ripple);
        setTimeout(() => ripple.remove(), 1000);

        selectedAsset = null;
    }
};
