<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#111111">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Bubble Drum Pad</title>
    
    <link rel="manifest" href="data:application/manifest+json,{
      \"name\": \"Bubble Drum Pad\",
      \"short_name\": \"DrumPad\",
      \"start_url\": \".\",
      \"display\": \"standalone\",
      \"background_color\": \"#111111\",
      \"theme_color\": \"#111111\",
      \"icons\": [
        { \"src\": \"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==\", \"sizes\": \"192x192\", \"type\": \"image/png\" }
      ]
    }">
    
    <style>
        body { margin:0; padding:0; background:#111; color:#f0f0f0; font-family:Arial,sans-serif; touch-action: manipulation; -webkit-tap-highlight-color: transparent; }
        #container { display: flex; flex-direction: column; height: 100vh; }
        #grid-container { flex: 1; position: relative; background: #000; overflow: hidden; }
        #controls { padding: 10px; background: #222; display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes flash { 0% { background-color: rgba(255,255,255,0.8); box-shadow: 0 0 10px rgba(255,255,255,0.8); } 50% { background-color: rgba(255,255,255,0.3); box-shadow: 0 0 5px rgba(255,255,255,0.3); } 100% { background-color: rgba(255,255,255,0.8); box-shadow: 0 0 10px rgba(255,255,255,0.8); } }
        @keyframes pulse { 0% { transform: scale(1); opacity: 0.7; } 50% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(1); opacity: 0.7; } }
        
        .bubble { 
            position: absolute; 
            border-radius: 50%; 
            background-color: rgba(100,100,100,0.6); 
            cursor: pointer; 
            transition: all 0.2s; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 10px; 
            color: transparent; 
            user-select: none;
            touch-action: manipulation;
        }
        .bubble.active { animation: flash 0.5s infinite; background-color: rgba(255,255,255,0.8); color: #000; }
        .bubble.pattern { animation: pulse 1s infinite; background-color: rgba(52, 152, 219, 0.8); color: #fff; }
        .bubble:hover { transform: scale(1.2); background-color: rgba(150,150,150,0.8); }
        .bubble:active { transform: scale(0.95); }
        
        .control-btn { background: #444; color: #f0f0f0; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-size: 14px; }
        .control-btn.active { background: #3498db; color: #fff; }
        .control-btn.preset { background: #27ae60; }
        .control-btn.preset.active { background: #2ecc71; }
        
        .tempo-control { display: flex; align-items: center; font-size: 14px; }
        .tempo-control input { width: 80px; padding: 8px; background: #333; color: #fff; border: 1px solid #666; border-radius: 6px; }
        
        @media (max-width: 768px) {
            .bubble { width: 50px !important; height: 50px !important; font-size: 12px !important; }
            .control-btn { padding: 12px 20px; font-size: 16px; }
            #controls { padding: 15px; }
        }
    </style>
</head>
<body>
    <div id="container">
        <div id="grid-container"></div>
        <div id="controls">
            <button id="play-btn" class="control-btn">Play</button>
            <button id="stop-btn" class="control-btn">Stop</button>
            <button id="clear-btn" class="control-btn">Clear</button>
            <button id="wave-btn" class="control-btn preset">Wave Play</button>
            <div class="tempo-control">
                <span>Tempo:</span>
                <input type="range" id="tempo" min="40" max="240" value="120">
                <span id="tempo-value">120 BPM</span>
            </div>
        </div>
    </div>

    <script>
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const sounds = [
            {name: 'Kick', freq: 150, type: 'sine', decay: 0.3},
            {name: 'Snare', freq: 200, noise: true, decay: 0.2},
            {name: 'HiHat', freq: 300, noise: true, decay: 0.05},
            {name: 'Clap', freq: 600, noise: true, decay: 0.1}
        ];
        
        function playSound(sound) {
            let source;
            const gain = audioCtx.createGain();
            gain.connect(audioCtx.destination);
            gain.gain.setValueAtTime(0.8, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + sound.decay);
            
            if (sound.noise) {
                const bufferSize = audioCtx.sampleRate * sound.decay;
                const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
                const data = buffer.getChannelData(0);
                for (let i = 0; i < bufferSize; i++) data[i] = Math.random() * 2 - 1;
                source = audioCtx.createBufferSource();
                source.buffer = buffer;
            } else {
                source = audioCtx.createOscillator();
                source.frequency.value = sound.freq;
                source.type = sound.type || 'sine';
            }
            source.connect(gain);
            source.start();
            source.stop(audioCtx.currentTime + sound.decay);
        }
        
        const rows = 4;
        const cols = 8;
        const grid = [];
        let playing = false;
        let waveMode = false;
        let currentStep = 0;
        let intervalId = null;
        const gridContainer = document.getElementById('grid-container');

        function createGrid() {
            gridContainer.innerHTML = '';
            grid.length = 0;
            const bubbleSize = window.innerWidth < 768 ? 50 : 60;
            const spacingX = (gridContainer.clientWidth - cols * bubbleSize) / (cols + 1);
            const spacingY = (gridContainer.clientHeight - rows * bubbleSize) / (rows + 1);
            
            for (let row = 0; row < rows; row++) {
                grid[row] = [];
                for (let col = 0; col < cols; col++) {
                    const bubble = document.createElement('div');
                    bubble.className = 'bubble';
                    bubble.style.width = bubble.style.height = bubbleSize + 'px';
                    bubble.style.left = (spacingX + col * (bubbleSize + spacingX)) + 'px';
                    bubble.style.top = (spacingY + row * (bubbleSize + spacingY)) + 'px';
                    
                    const trigger = (e) => {
                        if (e) e.preventDefault();
                        playSound(sounds[row]);
                        bubble.classList.add('active');
                        setTimeout(() => bubble.classList.remove('active'), 200);
                    };
                    bubble.addEventListener('touchstart', trigger);
                    bubble.addEventListener('click', trigger);
                    
                    gridContainer.appendChild(bubble);
                    grid[row][col] = bubble;
                }
            }
        }

        document.getElementById('play-btn').onclick = () => {
            if (playing) return;
            playing = true;
            const tempo = document.getElementById('tempo').value;
            const stepTime = 60000 / (tempo * 4);
            intervalId = setInterval(() => {
                for (let r = 0; r < rows; r++) {
                    const b = grid[r][currentStep];
                    b.classList.add('pattern');
                    playSound(sounds[r]);
                    setTimeout(() => b.classList.remove('pattern'), 300);
                }
                currentStep = (currentStep + 1) % cols;
            }, stepTime);
        };

        document.getElementById('stop-btn').onclick = () => {
            playing = false;
            clearInterval(intervalId);
            currentStep = 0;
        };

        window.onresize = createGrid;
        createGrid();
        
        document.body.addEventListener('touchstart', () => {
            if (audioCtx.state === 'suspended') audioCtx.resume();
        }, { once: true });
    </script>
</body>
</html>
