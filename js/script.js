/**
 * Gestion du compte à rebours pour les quiz
 * @param {number} duration - Durée en secondes
 * @param {HTMLElement} display - Élément HTML où afficher le temps
 */
function startTimer(duration, display) {
    let timer = duration;
    let minutes, seconds;

    const countdown = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (timer <= 60) {
            const badge = display.closest('.badge');
            if (badge) {
                badge.classList.remove('bg-info', 'text-dark');
                badge.classList.add('bg-danger', 'text-white');
            }
            display.style.fontWeight = 'bold';
        }

        if (--timer < 0) {
            clearInterval(countdown);
            display.textContent = "00:00";
            
            const form = document.getElementById("quiz-form");
            if (form) {
                alert("⏳ Temps écoulé ! Votre quiz va être validé automatiquement.");
                form.submit();
            }
        }
    }, 1000);
}


function confirmDelete(message) {
    return confirm(message || '⚠️ Êtes-vous sûr de vouloir supprimer cet élément ? cette action est irréversible.');
}


document.addEventListener('DOMContentLoaded', function () {
    
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
           
            if (typeof bootstrap !== 'undefined') {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            } else {
                alert.style.display = 'none'; 
            }
        }, 4000);
    });

    const path = window.location.pathname;
    const currentPage = path.substring(path.lastIndexOf('/') + 1);
    
    if (currentPage) {
        document.querySelectorAll('.nav-link').forEach(function (link) {
            const href = link.getAttribute('href');
            if (href) {
                const linkPage = href.split('/').pop();
                if (linkPage === currentPage) {
                    link.classList.add('active');
                }
            }
        });
    }
});