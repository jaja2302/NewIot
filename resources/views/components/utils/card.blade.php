<style>
    /* From Uiverse.io by MikeAndrewDesigner */
    .e-card {
        margin: 100px auto;
        background: transparent;
        box-shadow: 0px 8px 28px -9px rgba(0, 0, 0, 0.45);
        position: relative;
        width: 240px;
        height: 330px;
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

    .icon {
        width: 3em;
        margin-top: -1em;
        padding-bottom: 1em;
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
        font-size: 1.5em;
        font-weight: 600;
        margin-top: 2em;
    }

    .bottom-section {
        align-self: flex-end;
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

    .name {
        font-size: 14px;
        font-weight: 100;
        position: relative;
        top: 1em;
        text-transform: lowercase;
    }

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
</style>

<div class="e-card playing">
    <div class="image"></div>

    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>

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