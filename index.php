
<?php
require_once __DIR__ . "/includes/db.php";

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Resolve portfolio image paths
function portfolioImage($path) {
    $path = trim((string)($path ?? ""));

    if ($path === "") {
        return "";
    }

    // Allow existing full image URLs
    if (preg_match('~^https?://~i', $path)) {
        return $path;
    }

    // Reject unsupported URL schemes
    if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $path)) {
        return "";
    }

    $path = str_replace("\\", "/", $path);

    // Remove query strings and fragments from local paths
    $path = explode("?", $path, 2)[0];
    $path = explode("#", $path, 2)[0];

    // Normalize common project-relative paths
    $path = ltrim($path, "/");

    if (str_starts_with($path, "business-portfolio/")) {
        $path = substr(
            $path,
            strlen("business-portfolio/")
        );
    }

    // Prevent directory traversal
    if (
        $path === "" ||
        in_array("..", explode("/", $path), true)
    ) {
        return "";
    }

    return $path;
}

// Company information
$company = [];

$result = $conn->query(
    "SELECT * FROM company_info LIMIT 1"
);

if ($result && $result->num_rows > 0) {
    $company = $result->fetch_assoc();
}

// Active sections
$sections = [];

$result = $conn->query(
    "SELECT * FROM sections
     WHERE status = 1
     ORDER BY id ASC"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sections[$row["section_key"]] = $row;
    }
}

// Active services
$services = [];

$result = $conn->query(
    "SELECT * FROM services
     WHERE status = 1
     ORDER BY sort_order ASC, id ASC"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Portfolio projects
$portfolio = [];

$result = $conn->query(
    "SELECT * FROM portfolio_items
     WHERE status = 1
     ORDER BY id DESC"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $portfolio[] = $row;
    }
}

// Active FAQs
$faqs = [];

$result = $conn->query(
    "SELECT * FROM faqs
     WHERE status = 1
     ORDER BY id ASC"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $faqs[] = $row;
    }
}

// Website sections
$home = $sections["home"] ?? [];
$about = $sections["about"] ?? [];
$servicesSection = $sections["services"] ?? [];
$contact = $sections["contact"] ?? [];

// Branding
$siteName = "NexaTech";

// Contact details
$email = $company["email"] ?? "info@violetpro.net";
$phone = $company["phone"] ?? "71 528 881";

$address = $company["address"]
    ?? "Samhat Center, 3rd Floor, Office No. 302, Tyre, Lebanon";

$phoneLink = preg_replace('/\s+/', '', $phone);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e($siteName) ?> | Business Solutions
    </title>

    <meta
        name="description"
        content="NexaTech provides web development, business systems, applications, and digital solutions."
    >

    <link
        rel="stylesheet"
        href="assets/css/style.css?v=5"
    >
</head>

<body>

<!-- NAVIGATION -->
<header class="navbar">

    <a href="#home" class="logo">
        Nexa<span>Tech</span>
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
            <?= e(
                $home["subtitle"]
                ?? "Smart Technology. Better Business."
            ) ?>
        </p>

        <h1>
            <?= e(
                $home["title"]
                ?? "Better Solutions For Your Business"
            ) ?>
        </h1>

        <p class="hero-description">
            <?= e(
                $home["description"]
                ?? "We create modern websites, accounting systems, and applications for businesses."
            ) ?>
        </p>

        <a href="#services" class="btn">
            Get Started
        </a>

    </div>

    <div class="hero-image">

        <img
            src="<?= e($home['image'] ?: 'assets/images/banner.png') ?>"
            alt="NexaTech business technology illustration"
            class="home-main-image"
        >

    </div>

</section>

<!-- ABOUT -->
<section class="section about-section" id="about">

    <div class="section-heading">

        <span class="section-label">
            ABOUT US
        </span>

        <h2>
            <?= e($about["title"] ?? "Who We Are") ?>
        </h2>

        <p class="section-text">
            <?= e(
                $about["description"]
                ?? "We provide digital solutions to help businesses grow."
            ) ?>
        </p>

    </div>

    <div class="about-points">

        <div>
            <h3>Our Mission</h3>

            <p>
                To deliver reliable, modern technology solutions
                that help businesses grow.
            </p>
        </div>

        <div>
            <h3>What We Do</h3>

            <p>
                We develop websites, business systems,
                applications, and digital marketing solutions.
            </p>
        </div>

    </div>

</section>

<!-- SERVICES -->
<section class="section services" id="services">

    <div class="section-heading">

        <span class="section-label">
            WHAT WE DO
        </span>

        <h2>
            <?= e(
                $servicesSection["title"]
                ?? "Our Services"
            ) ?>
        </h2>

        <p class="section-text">
            <?= e(
                $servicesSection["description"]
                ?? "Discover our technology and digital services."
            ) ?>
        </p>

    </div>

    <div class="service-grid">

        <?php if (!empty($services)): ?>

            <?php foreach ($services as $index => $service): ?>

                <article class="service-card">

                    <div class="service-icon">
                        <?= sprintf("%02d", $index + 1) ?>
                    </div>

                    <h3>
                        <?= e($service["title"] ?? "") ?>
                    </h3>

                    <p>
                        <?= e($service["description"] ?? "") ?>
                    </p>

                </article>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                No services available at the moment.
            </p>

        <?php endif; ?>

    </div>

</section>

<!-- FAQ -->
<?php if (!empty($faqs)): ?>

<section class="section faq-section" id="faq">

    <div class="section-heading">

        <span class="section-label">
            FREQUENTLY ASKED QUESTIONS
        </span>

        <h2>
            Questions? We Have Answers
        </h2>

        <p class="section-text">
            Find answers to common questions.
        </p>

    </div>

    <div class="faq-list">

        <?php foreach ($faqs as $faq): ?>

            <details class="faq-item">

                <summary>
                    <?= e($faq["question"] ?? "") ?>
                </summary>

                <p>
                    <?= e($faq["answer"] ?? "") ?>
                </p>

            </details>

        <?php endforeach; ?>

    </div>

</section>

<?php endif; ?>

<!-- PORTFOLIO -->
<?php if (!empty($portfolio)): ?>

<section
    class="section portfolio-section"
    id="portfolio"
>

    <div class="section-heading">

        <span class="section-label">
            OUR WORK
        </span>

        <h2>Portfolio</h2>

        <p class="section-text">
            Explore our projects and digital solutions.
        </p>

    </div>

    <!-- Portfolio Filters -->
    <div class="portfolio-filters">

        <button
            type="button"
            class="filter-btn active"
            data-filter="all"
        >
            All
        </button>

        <button
            type="button"
            class="filter-btn"
            data-filter="web"
        >
            Web
        </button>

        <button
            type="button"
            class="filter-btn"
            data-filter="app"
        >
            App
        </button>

        <button
            type="button"
            class="filter-btn"
            data-filter="marketing"
        >
            Marketing
        </button>

    </div>

    <!-- Portfolio Cards -->
    <div class="portfolio-grid">

        <?php foreach ($portfolio as $item): ?>

            <?php
            $category = strtolower(
                trim((string)($item["category"] ?? "web"))
            );

            $imagePath = portfolioImage(
                $item["image"] ?? ""
            );
            ?>

            <article
                class="portfolio-card"
                data-category="<?= e($category) ?>"
            >

                <?php if ($imagePath !== ""): ?>

                    <img
                        src="<?= e($imagePath) ?>"
                        alt="<?= e($item["title"] ?? "Project") ?>"
                        loading="lazy"
                    >

                <?php else: ?>

                    <div class="portfolio-placeholder">
                        <?= e(strtoupper($category)) ?>
                    </div>

                <?php endif; ?>

                <div class="portfolio-info">

                    <h3>
                        <?= e($item["title"] ?? "") ?>
                    </h3>

                    <?php if (!empty($item["description"])): ?>

                        <p>
                            <?= e($item["description"]) ?>
                        </p>

                    <?php endif; ?>

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
<section
    class="section contact-section"
    id="contact"
>

    <div class="section-heading">

        <span class="section-label">
            GET IN TOUCH
        </span>

        <h2>
            <?= e($contact["title"] ?? "Contact Us") ?>
        </h2>

        <p class="section-text">
            <?= e(
                $contact["description"]
                ?? "Contact our team to learn more about our services."
            ) ?>
        </p>

    </div>

    <div class="contact-card">

        <p>
            <strong>Company:</strong>
            <?= e($siteName) ?>
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
        &copy; <?= date("Y") ?>
        <?= e($siteName) ?>.
        All rights reserved.
    </p>

</footer>

<!-- PORTFOLIO FILTER -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const buttons = document.querySelectorAll(".filter-btn");
    const cards = document.querySelectorAll(".portfolio-card");

    buttons.forEach(function (button) {

        button.addEventListener("click", function () {

            const filter = button.dataset.filter;

            buttons.forEach(function (item) {
                item.classList.toggle(
                    "active",
                    item === button
                );
            });

            cards.forEach(function (card) {

                card.hidden =
                    filter !== "all" &&
                    card.dataset.category !== filter;

            });

        });

    });

});
</script>

</body>
</html>
