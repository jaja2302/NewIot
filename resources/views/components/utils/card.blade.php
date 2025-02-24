<!-- The styling -->
<style>
    .e-card {
        margin: 0;
        background: transparent;
        box-shadow: 0px 8px 28px -9px rgba(0, 0, 0, 0.45);
        position: relative;
        width: 100%;
        height: 280px;
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
        align-items: center;
        justify-content: space-between;
        height: 85%;
        width: 100%;
        color: rgb(255, 255, 255);
        padding: 1.5em;
    }

    .title {
        font-size: 1.3em;
        font-weight: 600;
        margin-top: 0.5em;
        line-height: 1.2;
        width: 100%;
        text-align: center;
    }

    .bottom-section {
        width: 100%;
        text-align: center;
        margin-bottom: 1em;
    }

    .row1 {
        display: flex;
        justify-content: center;
    }

    .item {
        display: flex;
        flex-direction: column;
        gap: 0.5em;
        align-items: center;
    }

    .big-text {
        font-size: 1.1em;
        font-weight: 600;
    }

    .value-container {
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .number-text {
        font-size: 3em;
        font-weight: 600;
        line-height: 1;
    }

    .unit-text {
        font-size: 0.9em;
        margin-left: 0.3em;
        margin-bottom: 0.5em;
        opacity: 0.9;
    }

    .wave {
        border-radius: 40%;
        animation: wave 55s infinite linear;
    }

    .wave:nth-child(2),
    .wave:nth-child(3) {
        top: 210px;
    }

    .wave:nth-child(2) {
        animation-duration: 50s;
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

<!-- The Blade component -->
<div class="e-card playing">
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="infotop">
        <span class="title">{{ $estate['name'] }}</span>
        <div class="bottom-section">
            <div class="row row1">
                <div class="item">
                    <span class="big-text">Level Parit</span>
                    <div class="value-container">
                        <span class="number-text">{{ (int)$estate['level_parit'] }}</span>
                        <span class="unit-text">cm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>