class musicPlayer {
    constructor(les_musiques) {
        // Vérifier si aucune musique n'est présente
        if (les_musiques.length === 0) {
            console.error("Aucune musique n'est disponible dans cet album.");
            // Afficher un message d'erreur dans l'interface utilisateur si nécessaire
            return;
        }
        this.les_musiques = les_musiques;
        this.index_current_musique = 0;
        this.audio = document.getElementById("audio");
        this.audio.src = "../static/sounds/" + this.les_musiques[this.index_current_musique];
        this.playBtn = document.getElementById('play');
        this.playBtn.addEventListener('click', this.play.bind(this));
        this.nextBtn = document.getElementById('next');
        this.nextBtn.addEventListener('click', () => this.nextTrack());
        this.prevBtn = document.getElementById('prev');
        this.prevBtn.addEventListener('click', () => this.previousTrack());
        this.restartBtn = document.getElementById('restart');
        this.restartBtn.addEventListener('click', () => this.restartTrack());
        this.controlPanel = document.getElementById('control-panel');
        this.infoBar = document.getElementById('info');
        // Appel de la méthode pour afficher la liste d'attente actuelle des musiques
        this.updateQueueList();

        this.currentTimeElement = document.getElementById("current-time");
        this.totalTimeElement = document.getElementById("total-time");
        // Mettre à jour la position du slider en fonction de la progression de lecture du son
        this.audio.addEventListener("timeupdate", this.updateDureeMusique.bind(this));

        // Gérer l'événement d'erreur lors du chargement de la musique
        this.audio.addEventListener('error', () => {
            console.error("Erreur lors du chargement de la musique.");
            // Afficher un message d'erreur dans l'interface utilisateur si nécessaire
        });

        // Initilisation des informations sur la piste en cours de lecture
        const nom_son_actuel = this.les_musiques[this.index_current_musique];
        const liste_mots_nom_musique = nom_son_actuel.replace(".mp3", "").replace(/-/g, " ").split(" ");
        const nom_musique_actuelle = liste_mots_nom_musique.map(mot => {
            // Capitaliser la première lettre de chaque mot
            return mot.charAt(0).toUpperCase() + mot.slice(1);
        }).join(" "); // Joindre les mots avec un espace
        this.infoBar.querySelector('.name').textContent = nom_musique_actuelle;

        // Écouter l'événement loadedmetadata pour mettre à jour la durée totale au départ
        this.audio.addEventListener('loadedmetadata', () => {
            this.totalTimeElement.textContent = formatTime(this.audio.duration);
        });

        this.progressbar = document.querySelector('.progress-bar .bar');
        this.audio.addEventListener('timeupdate', this.updateProgressbar.bind(this));

        // Référence au slider de contrôle du volume
        this.volumeSlider = document.getElementById('volume-slider');

        // Écouteur d'événement pour le contrôle de volume
        this.volumeSlider.addEventListener('input', () => this.adjustVolume());
    }

    adjustVolume() {
        this.audio.volume = this.volumeSlider.value / 100;
    }

    updateDureeMusique(){
        // Mettre à jour le temps écoulé
        var currentTime = formatTime(audio.currentTime);
        this.currentTimeElement.textContent = currentTime;

        // Mettre à jour le temps total
        var totalTime = formatTime(this.audio.duration);
        this.totalTimeElement.textContent = totalTime;
    }

    updateQueueList() {
        const queueList = document.getElementById('file-attente');
        // Efface la liste actuelle
        queueList.innerHTML = "Liste d'attente";
        // Ajoute chaque musique à la liste
        for (let index_file_attente = this.index_current_musique + 1; index_file_attente < this.les_musiques.length; index_file_attente++){
            const listItem = document.createElement('li');
            const nom_son_actuel = this.les_musiques[index_file_attente];
            const liste_mots_nom_musique = nom_son_actuel.replace(".mp3", "").replace(/-/g, " ").split(" ");
            const nom_musique_actuelle = liste_mots_nom_musique.map(mot => {
                // Capitaliser la première lettre de chaque mot
                return mot.charAt(0).toUpperCase() + mot.slice(1);
            }).join(" "); // Joindre les mots avec un espace
            listItem.textContent = nom_musique_actuelle;
            queueList.appendChild(listItem);   
        }
    }

    play() {
        let controlPanelObj = this.controlPanel,
            infoBarObj = this.infoBar;
        if (this.audio.paused) {
            this.audio.play();
        }
        else {
            this.audio.pause();
        }
        // Vérifier si le son est en pause avant de modifier les classes CSS
        if (!this.audio.paused) {
            console.log("Son en écoute");
            controlPanelObj.classList.add('active');
            infoBarObj.classList.add('active');
        }
        else {
            controlPanelObj.classList.remove('active');
            infoBarObj.classList.remove('active');
        }
    }

    nextTrack() {
        // Vérifier si la piste actuelle n'est pas la dernière piste de l'album
        if (this.index_current_musique < this.les_musiques.length - 1) {
            // Aller à la piste suivante
            this.index_current_musique += 1;
        }
        else {
            // Revenir à la première piste si la piste actuelle est la dernière
            this.index_current_musique = 0;
        }
        // Charger et jouer la nouvelle piste
        this.loadAndPlayTrack();
    }

    previousTrack() {
        // Vérifier si la piste actuelle n'est pas la première piste de l'album
        if (this.index_current_musique == 0) {
            // Aller à la dernière piste
            this.index_current_musique = this.les_musiques.length - 1;
        }
        else {
            // Revenir à la première piste si la piste actuelle est la dernière
            this.index_current_musique -= 1;
        }
        // Charger et jouer la nouvelle piste
        this.loadAndPlayTrack();
    }

    loadAndPlayTrack() {
        var nouvelle_track_a_relancer = false;
        if (!this.audio.paused){
            nouvelle_track_a_relancer = true;
        }
        // Charger la nouvelle piste
        this.audio.src = "../static/sounds/" + this.les_musiques[this.index_current_musique];
        // Réinitialiser le lecteur audio
        this.audio.currentTime = 0;
        if (nouvelle_track_a_relancer){
            // Relancer la lecture de la nouvelle piste
            this.audio.play();
        }
        // Mettre à jour les informations sur la piste en cours de lecture
        const nom_son_actuel = this.les_musiques[this.index_current_musique];
        const liste_mots_nom_musique = nom_son_actuel.replace(".mp3", "").replace(/-/g, " ").split(" ");
        const nom_musique_actuelle = liste_mots_nom_musique.map(mot => {
            // Capitaliser la première lettre de chaque mot
            return mot.charAt(0).toUpperCase() + mot.slice(1);
        }).join(" "); // Joindre les mots avec un espace
        this.infoBar.querySelector('.name').textContent = nom_musique_actuelle;
        // Mise à jour de la file d'attente des musiques
        this.updateQueueList();
    }

    restartTrack() {
        this.audio.currentTime = 0;
        // Vérifier si la musique est en pause
        if (!this.audio.paused) {
            // Si la musique est en cours de lecture, relancer la musique
            this.audio.play();
        }
    }    

    updateProgressbar() {
        const currentTime = this.audio.currentTime;
        const duration = this.audio.duration;
        const progress = (currentTime / duration) * 100;
        this.progressbar.style.width = progress + '%';
    }
}

// Fonction pour formater le temps au format MM:SS
function formatTime(time) {
    var minutes = Math.floor(time / 60);
    var seconds = Math.floor(time % 60);
    return pad(minutes) + ":" + pad(seconds);
}

// Fonction pour ajouter un zéro en tête si le chiffre est inférieur à 10
function pad(number) {
    return (number < 10 ? "0" : "") + number;
}

const newMusicplayer = new musicPlayer(musiques);