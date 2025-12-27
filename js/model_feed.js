async function loadPublicFeed() {
    const response = await fetch('/api/models.php');
    const files = await response.json();
    const container = document.getElementById('publicModelFeed');

    if (!container) return;

    // Add Search UI
    container.innerHTML = `
        <div class="p-2 border-b border-gray-700">
            <input type="text" id="modelSearch" placeholder="Search models..." 
                   class="w-full bg-black text-white text-[10px] p-1 border border-gray-600 rounded">
        </div>
        <div id="feedList" class="overflow-y-auto h-full p-2"></div>
    `;

    const feedList = document.getElementById('feedList');

    const renderList = (filter = '') => {
        feedList.innerHTML = '';
        files.filter(f => f.toLowerCase().includes(filter.toLowerCase())).forEach(file => {
            const name = file.split('/').pop();
            const card = document.createElement('div');
            card.draggable = true; card.className = "bg-gray-900 p-2 mb-2 rounded border border-gray-700 hover:border-red-500 cursor-pointer";
            card.innerHTML = `
                <p class="text-[9px] text-gray-400 truncate">${name}</p>
                <button onclick="loadModelIntoCanvas('${file.replace('../', '')}')" 
                        class="w-full mt-1 bg-red-900 hover:bg-red-700 text-[9px] py-1 rounded transition">
                    Load Model
                </button>`;
            feedList.appendChild(card);
        });
    };

    document.getElementById('modelSearch').oninput = (e) => renderList(e.target.value);
    renderList();
}
document.addEventListener('DOMContentLoaded', loadPublicFeed);
document.addEventListener('dragstart', (e) => {
    if (e.target.closest('.bg-gray-900')) {
        const file = e.target.querySelector('button').getAttribute('onclick').match(/'([^']+)'/)[1];
        e.dataTransfer.setData('text/plain', file);
    }
});
