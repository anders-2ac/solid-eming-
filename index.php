<?php
// Inclure la connexion à la base de données et les utilitaires
require_once 'includes/db.php';
require_once 'includes/utilities.php'; 

// Initialiser la session pour vérifier la connexion utilisateur
$isLoggedIn = isset($_SESSION['user_id']);

// Récupérer les services disponibles
$query = "SELECT id, nom, description, category, image_url FROM services WHERE availability = 1";
$services = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | Conciergerie de Luxe</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/main.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Section d'accueil avec image de fond -->
    <section id="hero" class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Conciergerie de Luxe pour Locations Courte et Moyenne Durée</h1>
            <p class="hero-subtitle">Optimisez la gestion de votre propriété avec nos services sur mesure</p>
            <a href="#services" class="cta-button">Découvrez nos services</a>
        </div>
    </section>

    <!-- Appel à un devis -->
    <section id="cta" class="cta-section">
        <h2 class="cta-title">Prêt à améliorer votre gestion immobilière ?</h2>
        <a href="devis.php" class="cta-button">Demander un devis</a>
    </section>

    <!-- Information sur la tarification -->
<section id="pricing-info" class="pricing-info">
    <p>Veuillez noter que les prix de nos services sont taxés en fonction des recettes locatives générées, et seront déterminés à partir des informations obtenues dans le devis personnalisé.</p>
</section>

<!-- Affichage des services -->
<section id="services" class="services">
    <h2>Nos Services</h2>
    <div class="service-list">
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <img src="<?= htmlspecialchars($service['image_url']) ?>" alt="<?= htmlspecialchars($service['nom']) ?>">
                <h3><?= htmlspecialchars($service['nom']) ?></h3>
                <p><strong>Catégorie :</strong> <?= htmlspecialchars($service['category']) ?></p>
                <p><?= htmlspecialchars($service['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>


    <!-- Témoignages des clients -->
<!-- Témoignages des clients -->
<section class="testimonials">
    <h2>Ce que disent nos clients</h2>
    <div class="testimonial-grid">
        <?php
        try {
            // Requête SQL sans la limitation des 5 derniers avis
            $query = "SELECT r.review, r.rating, u.username 
                      FROM reviews r 
                      JOIN users u ON r.user_id = u.id 
                      ORDER BY r.created_at DESC";

            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($reviews) {
                foreach ($reviews as $review) {
                    echo "<div class='testimonial-card'>
                            <p>" . htmlspecialchars($review['review']) . "</p>
                            <div class='rating'>";

                    // Afficher les étoiles en fonction de la note
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= (int)$review['rating'] 
                            ? "<span class='star filled'>&#9733;</span>" 
                            : "<span class='star'>&#9734;</span>";
                    }

                    echo "</div>
                          <p><strong>Par :</strong> " . htmlspecialchars($review['username']) . "</p>
                          </div>";
                }
            } else {
                // Message en cas d'absence de témoignages
                echo "<p>Aucun témoignage disponible pour le moment.</p>";
            }
        } catch (PDOException $e) {
            // Gestion des erreurs PDO
            echo "<p>Erreur lors de la récupération des avis : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
</section>


    <?php include 'includes/footer.php'; ?>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    // Animation au défilement
    const animateOnScroll = () => {
        const animatedSections = document.querySelectorAll('.cta-section, .services, .testimonials, .pricing-info');
        animatedSections.forEach(section => {
            const sectionPos = section.getBoundingClientRect().top;
            if (sectionPos < window.innerHeight / 1.2) {
                section.classList.add('visible');
            }
        });
    };

    // Ajout d'un effet de pulsation aux boutons
    const ctaButtons = document.querySelectorAll('.cta-button');
    ctaButtons.forEach(button => {
        button.addEventListener('mouseenter', () => {
            button.style.transform = 'scale(1.1)';
            button.style.boxShadow = '0 8px 15px rgba(0, 0, 0, 0.2)';
        });
        button.addEventListener('mouseleave', () => {
            button.style.transform = 'scale(1)';
            button.style.boxShadow = 'none';
        });
    });

    // Témoignages avec transition fluide
    let testimonialIndex = 0;
    const testimonials = document.querySelectorAll('.testimonial-card');
    if (testimonials.length > 0) {
        testimonials[testimonialIndex].style.display = 'block'; // Afficher le premier témoignage
        testimonials[testimonialIndex].style.opacity = 1;

        setInterval(() => {
            // Cacher le témoignage actuel
            testimonials[testimonialIndex].style.opacity = 0;
            setTimeout(() => {
                testimonials[testimonialIndex].style.display = 'none';

                // Passer au suivant
                testimonialIndex = (testimonialIndex + 1) % testimonials.length;

                // Afficher le prochain témoignage
                testimonials[testimonialIndex].style.display = 'block';
                testimonials[testimonialIndex].style.opacity = 1;
            }, 500); // Délai pour une transition fluide
        }, 5000); // Intervalle entre les témoignages
    }

    // Déclenchement des animations au défilement
    document.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Initialiser l'animation au chargement de la page

    // Smooth scroll pour les liens d'ancrage
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').slice(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const animateOnScroll = () => {
        const animatedSections = document.querySelectorAll('.cta-section, .services, .testimonials, .pricing-info');
        animatedSections.forEach(section => {
            const sectionPos = section.getBoundingClientRect().top;
            if (sectionPos < window.innerHeight / 1.2) {
                section.classList.add('visible');
            }
        });
    };

    document.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Initialiser l'animation au chargement
});

    </script>
    <style>
    /* Style global */
body {
    margin: 0;
    padding: 0;
    font-family: 'Roboto', sans-serif;
    background-color: #f7f7f7;
    color: #333;
}

h1, h2, h3 {
    font-family: 'Merriweather', serif;
    margin: 0.5em 0;
}

a {
    text-decoration: none;
    color: inherit;
}

a:hover {
    color: #007BFF;
}

button, .cta-button {
    background-color: #007BFF;
    color: white;
    border: none;
    padding: 12px 24px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

button:hover, .cta-button:hover {
    background-color: #0056b3;
}

/* Section héroïque */
.hero-section {
    height: 100vh;
    background: url('../images/hero-image.jpg') no-repeat center center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
}

.hero-title {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 10px;
}

.hero-subtitle {
    font-size: 24px;
    margin-bottom: 20px;
}

/* Section Appel à l'action (CTA) */
.cta-section {
    background-color: #0056b3;
    color: white;
    padding: 40px 0;
    text-align: center;
}

.cta-title {
    font-size: 32px;
    margin-bottom: 15px;
}

/* Section Services */
.services {
    padding: 60px 10%;
    background-color: #f7f7f7;
    text-align: center;
}

.service-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.service-card {
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s;
}

.service-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.service-card h3 {
    font-size: 22px;
    margin: 15px 0;
}

.service-card p {
    padding: 0 15px 15px;
}

.service-card:hover {
    transform: scale(1.05);
}

/* Section Témoignages */
.testimonials {
    padding: 60px 10%;
    background-color: #333;
    color: white;
    text-align: center;
}

.testimonial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.testimonial-card {
    background-color: #444;
    padding: 20px;
    border-radius: 8px;
    display: none; /* Géré par JavaScript */
}

.testimonial-card p {
    margin: 0 0 10px;
}
    
/* Section d'information sur la tarification */
.pricing-info {
    padding: 20px 10%;
    background-color: #f7f7f7;
    text-align: center;
    font-size: 18px;
    color: #333;
    margin-top: 20px;
}


/* Animations */
.visible {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.services-section,
.testimonials-section,
.cta-section {
    opacity: 0;
    transform: translateY(100px);
}
/* Transition et animation des sections */
.cta-section,
.services,
.testimonials,
.pricing-info {
    opacity: 0;
    transform: translateY(100px);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

/* Lorsqu'elles deviennent visibles */
.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Pulsation des boutons */
.cta-button {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Témoignages - transition fluide */
.testimonial-card {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

/* Icônes sociales pour plus d'interactivité */
.social-media img {
    transition: transform 0.4s ease, filter 0.3s ease;
}

.social-media img:hover {
    transform: scale(1.2);
    filter: brightness(1.2);
}

.rating {
    display: flex;
    gap: 2px;
    margin: 10px 0;
    justify-content: center;
}

.star {
    font-size: 18px;
    color: #ccc;
    transition: color 0.3s;
}

.star.filled {
    color: #ffd700; /* Couleur dorée */
}

.testimonials {
    padding: 60px 10%;
    background-color: #333;
    color: white;
    text-align: center;
}

.testimonial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.testimonial-card {
    background-color: #444;
    padding: 20px;
    border-radius: 8px;
    opacity: 0; /* Pour les animations */
    transform: translateY(100px);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

.testimonial-card.visible {
    opacity: 1;
    transform: translateY(0);
}

    </style>
</body>
</html>
