<!DOCTYPE html>
<html>
<head>
    <title>Advertisement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body { margin: 0; background: black; height: 100%; overflow: hidden; }
        #adContainer { width: 100vw; height: 100vh; position: relative; }
        #contentElement { width: 100%; height: 100%; }

        /* Top Right Overlay */
        .ad-overlay {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 100;
            font-family: sans-serif;
        }

        #skipBtn {
            padding: 12px 24px;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            display: none;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }

        #countdown {
            padding: 12px 20px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            border-radius: 4px;
            font-size: 14px;
            display: block;
        }
    </style>
</head>
<body>

<div id="adContainer">
    <video id="contentElement" playsinline muted></video>
    
    <div class="ad-overlay">
        <div id="countdown">Skip in 15s</div>
        <button id="skipBtn">Skip Ad &raquo;</button>
    </div>
</div>

<script src="https://imasdk.googleapis.com/js/sdkloader/ima3.js"></script>

<script>
    const redirectUrl = @json($redirect);

    // 🔴 VAST XML LINK HERE
    const adTagUrl = "https://baggymaintenance.com/d.mCFJz/dWGiNzvhZAGXUY/hevm_9PuNZMUulwkPP/TvYB5MNgD/Mry/MlT/c/tWNxjykG0TMhzyIvy/M/Qv";

    let adDisplayContainer, adsLoader, adsManager;
    const videoContent = document.getElementById('contentElement');
    const skipBtn = document.getElementById('skipBtn');
    const countdownEl = document.getElementById('countdown');
    let timerInterval;

    // Unified timer logic for both IMA and Local Video
    function runCountdown() {
        let timeLeft = 15;
        countdownEl.style.display = 'block';
        skipBtn.style.display = 'none';
        countdownEl.textContent = `Skip in ${timeLeft}s`;

        if (timerInterval) clearInterval(timerInterval);

        timerInterval = setInterval(() => {
            timeLeft--;
            countdownEl.textContent = `Skip in ${timeLeft}s`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                countdownEl.style.display = 'none';
                skipBtn.style.display = 'block';
            }
        }, 1000);
    }

    function initIMA() {
        videoContent.muted = true; 
        adDisplayContainer = new google.ima.AdDisplayContainer(document.getElementById('adContainer'), videoContent);
        adsLoader = new google.ima.AdsLoader(adDisplayContainer);

        adsLoader.addEventListener(google.ima.AdsManagerLoadedEvent.Type.ADS_MANAGER_LOADED, onAdsManagerLoaded);

        adsLoader.addEventListener(google.ima.AdErrorEvent.Type.AD_ERROR, function(adErrorEvent) {
            console.warn("IMA Error, falling back to local ad:", adErrorEvent.getError().getMessage());
            
            // Local fallback (ensure ads.mp4 is in the same folder or root)
            videoContent.src = "{{ asset('storage/ads/ads.mp4') }}"; 
            videoContent.load();
            videoContent.play().then(() => {
                runCountdown(); 
                videoContent.onended = redirectNow;
            }).catch(redirectNow);
        });

        const adsRequest = new google.ima.AdsRequest();
        adsRequest.adTagUrl = adTagUrl;
        adsRequest.linearAdSlotWidth = window.innerWidth;
        adsRequest.linearAdSlotHeight = window.innerHeight;
        adsLoader.requestAds(adsRequest);
    }

    function onAdsManagerLoaded(event) {
        adsManager = event.getAdsManager(videoContent);
        adsManager.addEventListener(google.ima.AdEvent.Type.ALL_ADS_COMPLETED, redirectNow);
        adsManager.addEventListener(google.ima.AdEvent.Type.CONTENT_RESUME_REQUESTED, redirectNow);

        // Start 15s visual countdown when ad actually starts
        adsManager.addEventListener(google.ima.AdEvent.Type.STARTED, runCountdown);

        adsManager.init(window.innerWidth, window.innerHeight, google.ima.ViewMode.NORMAL);
        adsManager.start();
    }

    function redirectNow() {
        window.location.href = redirectUrl;
    }

    skipBtn.onclick = function () {
        if (adsManager) {
            try { adsManager.skip(); } catch(e) {}
        }
        redirectNow();
    };

    window.onload = () => {
        adDisplayContainer = new google.ima.AdDisplayContainer(document.getElementById('adContainer'), videoContent);
        adDisplayContainer.initialize(); 
        initIMA();
    };
</script>
</body>
</html>
