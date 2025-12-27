async function loadPublicFeed() {
    const response = await fetch('/api/models.php');
    const files = await response.json();
    const container = document.querySelector('.red-square-selector'); // Replace with actual class/ID of red box

    if (!container) return;
    container.innerHTML = '<h3 class="text-white text-xs mb-2">Public 3D Feed</h3>';
    container.style.overflowY = 'auto';

    files.forEach(file => {
        const name = file.split('/').pop();
        const card = document.createElement('div');
        card.className = "bg-gray-800 p-2 mb-2 rounded cursor-pointer hover:border-red-500 border border-transparent transition";
        card.innerHTML = `
            <p class="text-[10px] text-gray-300 truncate">${name}</p>
            <button class="w-full mt-1 bg-red-600 text-[10px] py-1 rounded" 
                    onclick="loadModelIntoCanvas('${file.replace('../', '')}')">View in 3D</button>
        `;
        container.appendChild(card);
    });
}
document.addEventListener('DOMContentLoaded', loadPublicFeed);
