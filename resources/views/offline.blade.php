<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Kamu sedang offline — JogjaTrip</title>
        <link rel="icon" href="/icons/icon-192.png">
        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 1rem;
                padding: 24px;
                text-align: center;
                background: #FAF8F3;
                color: #3A3A3A;
                font-family: 'Figtree', system-ui, -apple-system, sans-serif;
            }
            .logo { font-size: 1.75rem; font-weight: 700; color: #2F6B5A; }
            .logo span { color: #B56E80; }
            .icon { font-size: 3rem; }
            h1 { font-size: 1.25rem; margin: 0; }
            p { color: #3A3A3A99; max-width: 26rem; margin: 0; font-size: 0.9rem; }
            button {
                margin-top: 0.5rem;
                background: #2F6B5A;
                color: white;
                border: none;
                border-radius: 0.75rem;
                padding: 0.75rem 1.5rem;
                font-size: 0.875rem;
                font-weight: 600;
                cursor: pointer;
            }
            button:hover { background: #244F42; }
        </style>
    </head>
    <body>
        <p class="logo">Jogja<span>Trip</span></p>
        <p class="icon">📡</p>
        <h1>Kamu sedang offline</h1>
        <p>Halaman ini belum sempat tersimpan untuk diakses offline. Itinerary yang pernah kamu buka sebelumnya biasanya tetap bisa dilihat tanpa koneksi internet.</p>
        <button onclick="location.reload()">Coba Lagi</button>
    </body>
</html>
