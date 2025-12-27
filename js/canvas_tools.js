// 1. Screenshot Function
async function takeScreenshot() {
    const element = document.getElementById("rallyView");
    const canvas = await html2canvas(element);
    const link = document.createElement('a');
    link.download = 'rally-design.png';
    link.href = canvas.toDataURL();
    link.click();
}

// 2. Paste from Clipboard
async function pasteToCanvas() {
    try {
        const items = await navigator.clipboard.read();
        for (const item of items) {
            if (item.types.includes("image/png") || item.types.includes("image/jpeg")) {
                const blob = await item.getType(item.types[0]);
                const reader = new FileReader();
                reader.onload = (e) => loadTextureToModel(e.target.result);
                reader.readAsDataURL(blob);
            }
        }
    } catch (err) {
        alert("Clipboard access denied or empty.");
    }
}

// 3. Easy 3D Viewer Loader
function loadModelIntoCanvas(url) {
    const loader = url.endsWith('.gltf') || url.endsWith('.glb') ? new THREE.GLTFLoader() : new THREE.OBJLoader();
    loader.load(url, (object) => {
        const model = object.scene || object;
        scene.add(model);
        // Center camera on new model
        const box = new THREE.Box3().setFromObject(model);
        const center = box.getCenter(new THREE.Vector3());
        controls.target.copy(center);
    });
}
