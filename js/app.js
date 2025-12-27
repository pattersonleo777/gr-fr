window.addEventListener('load', () => {
    document.getElementById('loadingOverlay').classList.add('hidden');
    console.log("Fantasy Rally Loaded");
    loadGallery();
});

document.getElementById('enterRallyButton').addEventListener('click', () => {
    document.getElementById('homeView').classList.add('hidden');
    document.getElementById('rallyView').classList.remove('hidden');
});
async function uploadFile() {
    const fileInput = document.getElementById('userFileUpload');
    if (fileInput.files.length === 0) return alert('Select a file');
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('type', 'images');

    const res = await fetch('api/upload.php', {
        method: 'POST',
        body: formData
    });
    const result = await res.json();
    if (result.success) alert('Uploaded to: ' + result.path);
    else alert('Upload failed');
}
async function uploadFile() {
    const fileInput = document.getElementById('userFileUpload');
    if (fileInput.files.length === 0) return alert('Select a file');
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('type', 'images');

    const res = await fetch('api/upload.php', {
        method: 'POST',
        body: formData
    });
    const result = await res.json();
    if (result.success) alert('Uploaded to: ' + result.path);
    else alert('Upload failed');
}
async function loadGallery() {
    const grid = document.getElementById('galleryGrid');
    // For now, we manually check the uploads folder via a simple list
    // In a full build, you would fetch this list from a PHP API
    const response = await fetch('api/list_assets.php');
    const assets = await response.json();
    
    grid.innerHTML = '';
    assets.forEach(asset => {
        const img = document.createElement('img');
        img.src = asset.path;
        img.className = 'w-full h-16 object-cover rounded border border-gray-600 hover:border-cyan-400 cursor-pointer';
        img.onclick = () => console.log('Selected:', asset.path);
        grid.appendChild(img);
    });
}
window.addEventListener('resize', () => {
    const container = document.getElementById('rallyView');
    if (!container || !camera || !renderer) return;
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});
window.addEventListener('resize', () => {
    const container = document.getElementById('rallyView');
    if (!container || !camera || !renderer) return;
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});
window.addEventListener('resize', () => {
    const container = document.getElementById('rallyView');
    if (!container || !camera || !renderer) return;
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});
