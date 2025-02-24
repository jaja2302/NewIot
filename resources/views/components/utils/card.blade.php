<style>
    /* From Uiverse.io by MikeAndrewDesigner */
    .e-card {
        /* Hilangkan margin auto dan sesuaikan ukuran */
        margin: 0;
        /* Ubah dari margin: 100px auto */
        background: transparent;
        box-shadow: 0px 8px 28px -9px rgba(0, 0, 0, 0.45);
        position: relative;
        width: 100%;
        /* Ubah dari width: 240px agar responsif */
        height: 280px;
        /* Sesuaikan height agar lebih compact */
        border-radius: 16px;
        overflow: hidden;
    }

    .wave {
        position: absolute;
        width: 540px;
        height: 700px;
        opacity: 0.6;
        left: 0;
        top: 0;
        margin-left: -50%;
        margin-top: -70%;
        background: linear-gradient(744deg, #af40ff, #5b42f3 60%, #00ddeb);
    }

    .infotop {
        text-align: center;
        position: absolute;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 85%;
        width: 100%;
        color: rgb(255, 255, 255);
        padding: 1.5em;
    }

    .title {
        font-size: 1.3em;
        /* Sedikit lebih kecil */
        font-weight: 600;
        margin-top: 1em;
        /* Kurangi margin top */
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .bottom-section {
        align-self: flex-end;
        width: 100%;
    }

    .row1 {
        text-align: right;
    }

    .item {
        display: flex;
        flex-direction: column;
        gap: 0.3em;
    }

    .big-text {
        font-size: 1.1em;
        font-weight: 600;
    }

    .regular-text {
        font-size: 1em;
        opacity: 0.9;
    }

    /* Animation settings - tetap sama */
    .wave:nth-child(2),
    .wave:nth-child(3) {
        top: 210px;
    }

    .playing .wave {
        border-radius: 40%;
        animation: wave 3000ms infinite linear;
    }

    .wave {
        border-radius: 40%;
        animation: wave 55s infinite linear;
    }

    .playing .wave:nth-child(2) {
        animation-duration: 4000ms;
    }

    .wave:nth-child(2) {
        animation-duration: 50s;
    }

    .playing .wave:nth-child(3) {
        animation-duration: 5000ms;
    }

    .wave:nth-child(3) {
        animation-duration: 45s;
    }

    @keyframes wave {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Tambahan untuk status level air */
    .water-level-status {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-danger {
        background-color: rgba(254, 226, 226, 0.9);
        color: rgb(185, 28, 28);
    }

    .status-warning {
        background-color: rgba(254, 243, 199, 0.9);
        color: rgb(161, 98, 7);
    }

    .status-safe {
        background-color: rgba(209, 250, 229, 0.9);
        color: rgb(6, 95, 70);
    }
</style>

<div class="e-card playing">
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>

    @php
    $levelStatus = $estate['level_parit'] > 80 ? 'danger' : ($estate['level_parit'] > 50 ? 'warning' : 'safe');
    $statusText = $levelStatus === 'danger' ? 'Danger' : ($levelStatus === 'warning' ? 'Warning' : 'Safe');
    @endphp

    <div class="water-level-status status-{{ $levelStatus }}">
        {{ $statusText }}
    </div>

    <div class="infotop">
        <span class="title">{{ $estate['name'] }}</span>
        <div class="bottom-section">
            <div class="row row1">
                <div class="item">
                    <span class="big-text">🌊 Level Parit</span>
                    <span class="regular-text">{{ number_format($estate['level_parit'], 2) }} cm</span>
                </div>
            </div>
        </div>
    </div>
</div>