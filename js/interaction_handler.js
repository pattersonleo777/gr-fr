let selectedAsset = null;
let tapCount = 0;
let tapTimer = null;

// Fix Upload Button
document.querySelector('button[onclick*="upload"]').onclick = async (e) => {
    e.preventDefault();
    const fileInput = document.querySelector('input[type="file"]');
    if (!fileInput.files[0]) return;

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    const res = await fetch('/api/upload.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) { location.reload(); }
};

// Handle Asset Interaction (Single Tap to Toggle, Double Tap for 3D Card)
function handleAssetClick(path, element) {
    tapCount++;
    if (tapCount === 1) {
        tapTimer = setTimeout(() => {
            // Toggle Selection
            document.querySelectorAll('.asset-item').forEach(el => el.classList.remove('border-blue-500'));
            selectedAsset = path;
            element.classList.add('border-blue-500');
            tapCount = 0;
        }, 250);
    } else {
        clearTimeout(tapTimer);
        tapCount = 0;
        open3DCard(path); // Double Tap: Skyrim-style popup
    }
}

// Canvas Tap to Add
document.getElementById('rallyView').onclick = () => {
    if (selectedAsset) {
        if (selectedAsset.endsWith('.glb')) loadModelIntoCanvas(selectedAsset);
        else loadTextureToModel(selectedAsset);
        selectedAsset = null;
        document.querySelectorAll('.asset-item').forEach(el => el.classList.remove('border-blue-500'));
    }
};
