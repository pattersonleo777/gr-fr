let selectedAsset = null;
let tapCount = 0;

// Fix Upload Trigger
document.getElementById('uploadBtn').onclick = () => document.getElementById('assetUploadInput').click();

document.getElementById('assetUploadInput').onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    const res = await fetch('/api/upload.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) location.reload();
    else alert("Upload failed");
};

// Handle Asset Tap Logic
window.handleAssetTap = (path, el) => {
    tapCount++;
    setTimeout(() => {
        if (tapCount === 1) {
            // Single Tap: Select for Canvas
            document.querySelectorAll('.asset-card').forEach(c => c.style.border = "none");
            el.style.border = "2px solid #ef4444";
            selectedAsset = path;
        } else if (tapCount === 2) {
            // Double Tap: Skyrim 3D Card
            open3DCard(path);
        }
        tapCount = 0;
    }, 300);
};

// Canvas Tap to Place
document.getElementById('rallyView').onclick = () => {
    if (selectedAsset) {
        selectedAsset.endsWith('.glb') ? loadModelIntoCanvas(selectedAsset) : loadTextureToModel(selectedAsset);
        selectedAsset = null;
    }
};
