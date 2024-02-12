class musicPlayer {
    constructor() {
        this.audio = document.getElementById("audio");
        this.play = this.play.bind(this);
        this.playBtn = document.getElementById('play');
        this.playBtn.addEventListener('click', this.play);
        this.controlPanel = document.getElementById('control-panel');
        this.infoBar = document.getElementById('info');
        this.progressbar = document.querySelector('.progress-bar .bar');
        this.audio.addEventListener('timeupdate', this.updateProgressbar.bind(this));
    }

    play() {
        let controlPanelObj = this.controlPanel,
            infoBarObj = this.infoBar;
        if (this.audio.paused) {
            this.audio.play();
        } else {
            this.audio.pause();
        }
        Array.from(controlPanelObj.classList).find(function(element) {
            return element !== "active" ? controlPanelObj.classList.add('active') : controlPanelObj.classList.remove('active');
        });

        Array.from(infoBarObj.classList).find(function(element) {
            return element !== "active" ? infoBarObj.classList.add('active') : infoBarObj.classList.remove('active');
        });
    }

    updateProgressbar() {
        const currentTime = this.audio.currentTime;
        const duration = this.audio.duration;
        const progress = (currentTime / duration) * 100;
        this.progressbar.style.width = progress + '%';
    }
}

const newMusicplayer = new musicPlayer();