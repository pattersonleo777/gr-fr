document.querySelector('button[onclick*="upload"]').onclick = async (e) => {
    e.preventDefault();
    const fileInput = document.querySelector('input[type="file"]');
    if (!fileInput.files[0]) return alert("Select a file first");

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);

    const res = await fetch('/api/upload.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
        // 1. Add to Profile Gallery
        const gallery = document.getElementById('assetGallery') || document.body;
        const newAsset = document.createElement('img');
        newAsset.src = data.filePath;
        newAsset.className = "w-20 h-20 object-cover rounded border border-gray-600 m-1";
        gallery.appendChild(newAsset);

        // 2. Load into 3D Workspace (if using Three.js)
        if (window.loadTextureToModel) window.loadTextureToModel(data.filePath);
        
        alert("Uploaded and added to 3D workspace!");
    } else {
        alert("Upload failed: " + data.message);
    }
};
