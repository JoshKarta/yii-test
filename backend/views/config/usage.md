Method 1: Using the get() method

<?php
// Get single configuration value
$siteTitle = Yii::$app->configuration->get('site_title', 'Default Title', 'general');
$maintenanceMode = Yii::$app->configuration->get('maintenance_mode', false, 'system');
$contactEmail = Yii::$app->configuration->get('email', '', 'contact');
?>

<h1><?= Html::encode($siteTitle) ?></h1>

<?php if ($maintenanceMode): ?>

    <div class="alert alert-warning">
        Website is under maintenance
    </div>

<?php endif; ?>

<?php if ($contactEmail): ?>

    <p>Contact us: <?= Html::encode($contactEmail) ?></p>

<?php endif; ?>

---

Method 2: Using category-based magic getter

<?php
// Access configurations by category
$general = Yii::$app->configuration->general;
$system = Yii::$app->configuration->system;
$contact = Yii::$app->configuration->contact;
$social = Yii::$app->configuration->social_media;
?>

<!-- Using category properties -->
<h1><?= Html::encode($general->site_title ?? 'Default Title') ?></h1>
<p><?= Html::encode($general->site_description ?? '') ?></p>

<?php if ($system->maintenance_mode ?? false): ?>

    <div class="alert alert-warning">Maintenance Mode Active</div>

<?php endif; ?>

<!-- Contact information -->
<div class="contact-info">
    <?php if (isset($contact->email)): ?>
        <p>Email: <?= Html::encode($contact->email) ?></p>
    <?php endif; ?>
    
    <?php if (isset($contact->phone)): ?>
        <p>Phone: <?= Html::encode($contact->phone) ?></p>
    <?php endif; ?>
</div>

---

Method 3: Get all configurations from a category

<?php
// Get all configurations from a category
$socialConfigs = Yii::$app->configuration->getAll('social_media');
$themeConfigs = Yii::$app->configuration->getAll('theme');
$contactConfigs = Yii::$app->configuration->getAll('contact');
?>

<!-- Social media links -->
<div class="social-links">
    <?php foreach ($socialConfigs as $platform => $url): ?>
        <?php if (!empty($url)): ?>
            <a href="<?= Html::encode($url) ?>" target="_blank" class="social-link">
                <i class="fab fa-<?= $platform ?>"></i>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Theme configurations -->
<style>
:root {
    --primary-color: <?= $themeConfigs['primary_color'] ?? '#007bff' ?>;
    --secondary-color: <?= $themeConfigs['secondary_color'] ?? '#6c757d' ?>;
    --font-family: <?= $themeConfigs['font_family'] ?? 'Arial, sans-serif' ?>;
}
</style>

---

Complete Layout Example

<?php
// frontend/views/layouts/main.php
use yii\helpers\Html;
use yii\helpers\Url;

// Get configurations by category
$general = Yii::$app->configuration->general;
$system = Yii::$app->configuration->system;
$contact = Yii::$app->configuration->contact;
$social = Yii::$app->configuration->social_media;
$theme = Yii::$app->configuration->theme;

// Or get all configs from categories
$socialConfigs = Yii::$app->configuration->getAll('social_media');
?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($general->site_title ?? 'My Website') ?></title>
    
    <!-- Dynamic theme styles -->
    <style>
    :root {
        --primary-color: <?= $theme->primary_color ?? '#007bff' ?>;
        --secondary-color: <?= $theme->secondary_color ?? '#6c757d' ?>;
        --font-family: <?= $theme->font_family ?? 'Arial, sans-serif' ?>;
        --header-bg: <?= $theme->header_background ?? '#f8f9fa' ?>;
    }
    
    .btn-primary { background-color: var(--primary-color); }
    .text-secondary { color: var(--secondary-color); }
    body { font-family: var(--font-family); }
    .header { background-color: var(--header-bg); }
    </style>
    
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
    <!-- Maintenance Mode Banner -->
    <?php if ($system->maintenance_mode ?? false): ?>
    <div class="maintenance-banner">
        <div class="container">
            <i class="fas fa-tools"></i>
            <span>Website is under maintenance. Some features may be unavailable.</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <!-- Site Logo -->
                    <?php if (isset($general->site_logo)): ?>
                        <img src="<?= Html::encode($general->site_logo) ?>"
                             alt="<?= Html::encode($general->site_title ?? 'Logo') ?>"
                             class="logo">
                    <?php else: ?>
                        <h1 class="site-title">
                            <?= Html::encode($general->site_title ?? 'My Website') ?>
                        </h1>
                    <?php endif; ?>
                </div>

                <div class="col-md-8 text-end">
                    <!-- Social Media Links -->
                    <?php if (!empty($socialConfigs)): ?>
                    <div class="social-links">
                        <?php foreach ($socialConfigs as $platform => $url): ?>
                            <?php if (!empty($url)): ?>
                            <a href="<?= Html::encode($url) ?>" target="_blank"
                               class="social-link" title="<?= ucfirst($platform) ?>">
                                <i class="fab fa-<?= $platform ?>"></i>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <!-- Copyright -->
                    <p>&copy; <?= date('Y') ?> <?= Html::encode($general->site_title ?? 'My Website') ?>. All rights reserved.</p>

                    <!-- Contact Information -->
                    <?php if (isset($contact->address)): ?>
                        <p class="footer-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= Html::encode($contact->address) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 text-end">
                    <!-- Contact Links -->
                    <div class="contact-links">
                        <?php if (isset($contact->email)): ?>
                            <a href="mailto:<?= Html::encode($contact->email) ?>" class="contact-link">
                                <i class="fas fa-envelope"></i>
                                <?= Html::encode($contact->email) ?>
                            </a>
                        <?php endif; ?>

                        <?php if (isset($contact->phone)): ?>
                            <a href="tel:<?= Html::encode($contact->phone) ?>" class="contact-link">
                                <i class="fas fa-phone"></i>
                                <?= Html::encode($contact->phone) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>

</body>
</html>

---

Contact Page View

<?php
// frontend/views/site/contact.php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$contactConfigs = Yii::$app->configuration->getAll('contact');
$formConfigs = Yii::$app->configuration->getAll('forms');
?>

<div class="contact-page">
    <h1>Contact Us</h1>
    
    <!-- Contact Information -->
    <div class="contact-info">
        <?php if (isset($contactConfigs['address'])): ?>
        <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <strong>Address:</strong> <?= Html::encode($contactConfigs['address']) ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($contactConfigs['phone'])): ?>
        <div class="contact-item">
            <i class="fas fa-phone"></i>
            <strong>Phone:</strong> <?= Html::encode($contactConfigs['phone']) ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($contactConfigs['email'])): ?>
        <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <strong>Email:</strong> <?= Html::encode($contactConfigs['email']) ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($contactConfigs['business_hours'])): ?>
        <div class="contact-item">
            <i class="fas fa-clock"></i>
            <strong>Business Hours:</strong> <?= Html::encode($contactConfigs['business_hours']) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Contact Form -->
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'subject') ?>
        <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

        <!-- Conditional CAPTCHA -->
        <?php if ($formConfigs['enable_captcha'] ?? true): ?>
            <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::class) ?>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>

---

Product Listing with Featured Items

<?php
// frontend/views/product/index.php
use yii\helpers\Html;

$productConfigs = Yii::$app->configuration->getAll('products');
$featuredProducts = $productConfigs['featured_products'] ?? [];
$productsPerPage = $productConfigs['products_per_page'] ?? 12;
$enableReviews = $productConfigs['enable_reviews'] ?? true;
?>

<div class="product-listing">
    <h1>Our Products</h1>
    
    <!-- Featured Products -->
    <?php if (!empty($featuredProducts)): ?>
    <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="row">
            <?php foreach ($featuredProducts as $productId): ?>
                <?= $this->render('_product_item', [
                    'productId' => $productId,
                    'featured' => true
                ]) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- All Products -->
    <section class="all-products">
        <div class="row">
            <?php foreach ($dataProvider->getModels() as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <?= $this->render('_product_item', [
                        'product' => $product,
                        'enableReviews' => $enableReviews
                    ]) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination justify-content-center'],
            'pageSize' => $productsPerPage
        ]) ?>
    </section>

</div>
