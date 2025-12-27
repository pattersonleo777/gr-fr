window.addEventListener('load', () => {
    document.getElementById('loadingOverlay').classList.add('hidden');
    console.log("Fantasy Rally Loaded");
});

document.getElementById('enterRallyButton').addEventListener('click', () => {
    document.getElementById('homeView').classList.add('hidden');
    document.getElementById('rallyView').classList.remove('hidden');
});
