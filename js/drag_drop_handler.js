const dropZone = document.getElementById('rallyView');

dropZone.addEventListener('dragover', (e) => e.preventDefault());

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    const filePath = e.dataTransfer.getData('text/plain');
    
    if (filePath.endsWith('.glb') || filePath.endsWith('.gltf')) {
        loadModelIntoCanvas(filePath);
    } else {
        // Assume it's a texture/image
        loadTextureToModel(filePath);
    }
});
