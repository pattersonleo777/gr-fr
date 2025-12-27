<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bubble Drum Pad</title>
    <style>
        body { margin:0; padding:0; background:#111; color:#f0f0f0; font-family:Arial,sans-serif; touch-action: manipulation; }
        #container { display: flex; flex-direction: column; height: 100vh; }
        #grid-container { flex: 1; position: relative; background: #000; overflow: hidden; }
        #controls { padding: 10px; background: #222; display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px; }
        .bubble { position: absolute; border-radius: 50%; background-color: rgba(100,100,100,0.6); cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .bubble.active { background-color: rgba(255,255,255,0.8); }
        .control-btn { background: #444; color: #f0f0f0; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; }
        .back-btn { position: absolute; top: 10px; left: 10px; z-index: 100; background: #ef4444; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>
    <a href="index.php" class="back-btn">← Back to Design</a>
    <div id="container">
        <div id="grid-container"></div>
        <div id="controls">
            <button id="play-btn" class="control-btn">Play</button>
            <button id="stop-btn" class="control-btn">Stop</button>
            <div style="color:white; font-size: 12px;">Tempo: <input type="range" id="tempo" min="40" max="240" value="120"></div>
        </div>
    </div>
    <script>
        // Drum Pad Logic
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const sounds = [
            {name: 'Kick', freq: 150, type: 'sine', decay: 0.3},
            {name: 'Snare', freq: 200, noise: true, decay: 0.2},
            {name: 'HiHat', freq: 300, noise: true, decay: 0.05},
            {name: 'Clap', freq: 600, noise: true, decay: 0.1}
        ];
        function playSound(sound) {
            let s; const g = audioCtx.createGain(); g.connect(audioCtx.destination);
            g.gain.setValueAtTime(0.8, audioCtx.currentTime); g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + sound.decay);
            if (sound.noise) {
                const b = audioCtx.createBuffer(1, audioCtx.sampleRate * sound.decay, audioCtx.sampleRate);
                const d = b.getChannelData(0); for (let i=0; i<d.length; i++) d[i] = Math.random()*2-1;
                s = audioCtx.createBufferSource(); s.buffer = b;
            } else {
                s = audioCtx.createOscillator(); s.frequency.value = sound.freq; s.type = sound.type || 'sine';
            }
            s.connect(g); s.start(); s.stop(audioCtx.currentTime + sound.decay);
        }
        const rows=4, cols=8, grid=[]; let currentStep=0, intervalId=null;
        const gridContainer = document.getElementById('grid-container');
        function createGrid() {
            gridContainer.innerHTML = ''; const size = 50;
            const spX = (gridContainer.clientWidth - cols * size) / (cols + 1);
            const spY = (gridContainer.clientHeight - rows * size) / (rows + 1);
            for (let r=0; r<rows; r++) {
                grid[r] = [];
                for (let c=0; c<cols; c++) {
                    const b = document.createElement('div'); b.className = 'bubble';
                    b.style.width = b.style.height = size + 'px';
                    b.style.left = (spX + c * (size + spX)) + 'px';
                    b.style.top = (spY + r * (size + spY)) + 'px';
                    b.onclick = () => { playSound(sounds[r]); b.classList.add('active'); setTimeout(()=>b.classList.remove('active'),200); };
                    gridContainer.appendChild(b); grid[r][c] = b;
                }
            }
        }
        document.getElementById('play-btn').onclick = () => {
            intervalId = setInterval(() => {
                for (let r=0; r<rows; r++) { playSound(sounds[r]); grid[r][currentStep].classList.add('active'); const cell = grid[r][currentStep]; setTimeout(()=>cell.classList.remove('active'),200); }
                currentStep = (currentStep + 1) % cols;
            }, 60000 / (document.getElementById('tempo').value * 4));
        };
        document.getElementById('stop-btn').onclick = () => { clearInterval(intervalId); currentStep = 0; };
        window.onresize = createGrid; createGrid();
        document.body.addEventListener('touchstart', () => audioCtx.resume(), { once: true });
    </script>
</body>
</html>
