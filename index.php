
<?php
require_once __DIR__ . '/includes/db.php';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Company information
$company = [];
$result = $conn->query("SELECT * FROM company_info LIMIT 1");

if ($result && $result->num_rows > 0) {
    $company = $result->fetch_assoc();
}

// Website sections
$sections = [];
$result = $conn->query("SELECT * FROM sections WHERE status = 1 ORDER BY id ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sections[$row['section_key']] = $row;
    }
}

// Services
$services = [];
$result = $conn->query(
    "SELECT * FROM services WHERE status = 1 ORDER BY sort_order ASC, id ASC"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Portfolio projects from database
$portfolio = [];
$portfolioTableExists = $conn->query("SHOW TABLES LIKE 'portfolio_items'");

if ($portfolioTableExists && $portfolioTableExists->num_rows > 0) {
    $result = $conn->query(
        "SELECT * FROM portfolio_items WHERE status = 1 ORDER BY sort_order ASC, id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $portfolio[] = $row;
        }
    }
}

// FAQs from database
$faqs = [];
$faqTableExists = $conn->query("SHOW TABLES LIKE 'faqs'");

if ($faqTableExists && $faqTableExists->num_rows > 0) {
    $result = $conn->query(
        "SELECT * FROM faqs WHERE status = 1 ORDER BY sort_order ASC, id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
    }
}

// Section data
$home = $sections['home'] ?? [];
$about = $sections['about'] ?? [];
$servicesSection = $sections['services'] ?? [];
$contact = $sections['contact'] ?? [];

// Company details
$companyName = $company['company_name'] ?? 'Violet Programming Company';
$email = $company['email'] ?? 'info@violetpro.net';
$phone = $company['phone'] ?? '71 528 881';
$address = $company['address'] ?? 'Tyre, Lebanon';

// Phone link
$phoneLink = preg_replace('/\s+/', '', $phone);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($companyName) ?> | Business Portfolio</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<!-- NAVIGATION -->
<header class="navbar">
    <a href="#home" class="logo">
        Violet<span>Programming</span>
    </a>

    <nav>
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#services">Services</a>

        <?php if (!empty($faqs)): ?>
            <a href="#faq">FAQ</a>
        <?php endif; ?>

        <?php if (!empty($portfolio)): ?>
            <a href="#portfolio">Portfolio</a>
        <?php endif; ?>

        <a href="#contact">Contact</a>
    </nav>
</header>

<main>

    <!-- HOME -->
    <section class="hero" id="home">
        <div class="hero-content">

            <p class="subtitle">
                <?= e($home['subtitle'] ?? 'We are a team of talented designers making websites with Bootstrap') ?>
            </p>

            <h1>
                <?= e($home['title'] ?? 'Better Solutions For Your Business') ?>
            </h1>

            <p>
                <?= e($home['description'] ?? 'We create modern websites, accounting systems, and applications for businesses.') ?>
            </p>

            <a href="#services" class="btn">Get Started</a>

        </div>
    </section>

    <!-- ABOUT -->
    <section class="section" id="about">

        <p class="section-label">ABOUT US</p>

        <h2>
            <?= e($about['title'] ?? 'Who We Are') ?>
        </h2>

        <p class="section-text">
            <?= e($about['description'] ?? 'We provide digital solutions to help businesses grow.') ?>
        </p>

    </section>

    <!-- SERVICES -->
    <section class="section services" id="services">

        <p class="section-label">WHAT WE DO</p>

        <h2>
            <?= e($servicesSection['title'] ?? 'Our Services') ?>
        </h2>

        <p class="section-text">
            <?= e($servicesSection['description'] ?? 'Discover our services for your business.') ?>
        </p>

        <div class="service-grid">

            <?php if (!empty($services)): ?>

                <?php foreach ($services as $index => $service): ?>

                    <article class="service-card">

                        <div class="service-icon">
                            <?= e(['01', '02', '03', '04'][$index % 4]) ?>
                        </div>

                        <h3>
                            <?= e($service['title'] ?? '') ?>
                        </h3>

                        <p>
                            <?= e($service['description'] ?? '') ?>
                        </p>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <p>No services are available at the moment.</p>

            <?php endif; ?>

        </div>

    </section>

    <!-- FAQ -->
    <?php if (!empty($faqs)): ?>

        <section class="section faq-section" id="faq">

            <p class="section-label">FREQUENTLY ASKED QUESTIONS</p>

            <h2>Questions? We Have Answers</h2>

            <p class="section-text">
                Find out more about our services and how to contact our team.
            </p>

            <div class="faq-list">

                <?php foreach ($faqs as $index => $faq): ?>

                    <details class="faq-item" <?= $index === 0 ? 'open' : '' ?>>

                        <summary>
                            <?= e($faq['question'] ?? '') ?>
                        </summary>

                        <p>
                            <?= e($faq['answer'] ?? '') ?>
                        </p>

                    </details>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endif; ?>

    <!-- PORTFOLIO -->
    <?php if (!empty($portfolio)): ?>

        <section class="section portfolio-section" id="portfolio">

            <p class="section-label">OUR WORK</p>

            <h2>Portfolio</h2>

            <p class="section-text">
                Explore our projects and design concepts.
            </p>

            <div class="portfolio-filters" aria-label="Filter portfolio">

                <button type="button" class="filter-btn active" data-filter="all">
                    All
                </button>

                <button type="button" class="filter-btn" data-filter="app">
                    App
                </button>

                <button type="button" class="filter-btn" data-filter="card">
                    Card
                </button>

                <button type="button" class="filter-btn" data-filter="web">
                    Web
                </button>

            </div>

            <div class="portfolio-grid">

                <?php foreach ($portfolio as $item): ?>

                    <?php
                    $category = strtolower((string)($item['category'] ?? 'web'));
                    $image = trim((string)($item['image'] ?? ''));
                    ?>

                    <article
                        class="portfolio-card"
                        data-category="<?= e($category) ?>"
                    >

                        <?php if ($image !== ''): ?>

                            <img
                                src="<?= e($image) ?>"
                                alt="<?= e($item['title'] ?? 'Portfolio project') ?>"
                                loading="lazy"
                            >

                        <?php endif; ?>

                        <div class="portfolio-info">

                            <h3>
                                <?= e($item['title'] ?? '') ?>
                            </h3>

                            <p>
                                <?= e($item['description'] ?? '') ?>
                            </p>

                            <span class="portfolio-category">
                                <?= e(strtoupper($category)) ?>
                            </span>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endif; ?>

    <!-- CONTACT -->
    <section class="section" id="contact">

        <p class="section-label">GET IN TOUCH</p>

        <h2>
            <?= e($contact['title'] ?? 'Contact Us') ?>
        </h2>

        <p class="section-text">
            <?= e($contact['description'] ?? 'Contact our team for more information.') ?>
        </p>

        <div class="contact-card">

            <p>
                <strong>Company:</strong>
                <?= e($companyName) ?>
            </p>

            <p>
                <strong>Email:</strong>
                <a href="mailto:<?= e($email) ?>">
                    <?= e($email) ?>
                </a>
            </p>

            <p>
                <strong>Phone:</strong>
                <a href="tel:<?= e($phoneLink) ?>">
                    <?= e($phone) ?>
                </a>
            </p>

            <p>
                <strong>Address:</strong>
                <?= e($address) ?>
            </p>

        </div>

    </section>

</main>

<!-- FOOTER -->
<footer>
    <p>
        &copy; <?= date('Y') ?>
        <?= e($companyName) ?>. All rights reserved.
    </p>
</footer>

<!-- PORTFOLIO FILTER -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterButtons = document.querySelectorAll('.filter-btn');
    const portfolioCards = document.querySelectorAll('.portfolio-card');

    filterButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            const filter = button.dataset.filter;

            filterButtons.forEach(function (item) {
                item.classList.toggle('active', item === button);
            });

            portfolioCards.forEach(function (card) {

                card.hidden =
                    filter !== 'all' &&
                    card.dataset.category !== filter;

            });

        });

    });

});
</script>

</body>
</html>
