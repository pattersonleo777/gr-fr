function open3DCard(imagePath) {
    const modal = document.createElement('div');
    modal.id = "cardModal";
    modal.className = "fixed inset-0 bg-black/80 flex items-center justify-center z-[999]";
    modal.innerHTML = '<div id="cardCanvas" class="w-[400px] h-[600px] cursor-grab"></div><button onclick="document.getElementById(\'cardModal\').remove()" class="absolute top-5 right-5 text-white">X Close</button>';
    document.body.appendChild(modal);

    const scene2 = new THREE.Scene();
    const camera2 = new THREE.PerspectiveCamera(45, 400/600, 0.1, 1000);
    const renderer2 = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer2.setSize(400, 600);
    document.getElementById('cardCanvas').appendChild(renderer2.domElement);

    // Create Card Geometry
    const geometry = new THREE.BoxGeometry(3, 4.5, 0.1);
    const loader = new THREE.TextureLoader();
    const frontTex = loader.load(imagePath);
    const backTex = loader.load('assets/ui/card_back.png'); // Add a generic back image

    const materials = [
        new THREE.MeshStandardMaterial({ color: 0x333333 }), // sides
        new THREE.MeshStandardMaterial({ color: 0x333333 }), 
        new THREE.MeshStandardMaterial({ color: 0x333333 }), 
        new THREE.MeshStandardMaterial({ color: 0x333333 }), 
        new THREE.MeshStandardMaterial({ map: frontTex }),   // front
        new THREE.MeshStandardMaterial({ map: backTex })     // back
    ];

    const card = new THREE.Mesh(geometry, materials);
    scene2.add(card);
    scene2.add(new THREE.AmbientLight(0xffffff, 1));
    camera2.position.z = 7;

    function animate() {
        if (!document.getElementById('cardModal')) return;
        requestAnimationFrame(animate);
        card.rotation.y += 0.01;
        renderer2.render(scene2, camera2);
    }
    animate();
}
