<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\models\MenuItem;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use kartik\bs5dropdown\Dropdown;
use webzop\notifications\widgets\Notifications;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$menuItems = [];
foreach (MenuItem::find()->where(['parent_id' => null])->orderBy('sort_order')->all() as $item) {
    $children = MenuItem::find()->where(['parent_id' => $item->id])->orderBy('sort_order')->all();
    if ($children) {
        $subItems = [];
        foreach ($children as $child) {
            $subItems[] = [
                'label' => $child->label,
                'url' => [$child->url],
            ];
        }
        $menuItems[] = [
            'label' => $item->label,
            'items' => $subItems,
        ];
    } else {
        $menuItems[] = [
            'label' => $item->label,
            'url' => [$item->url],
        ];
    }
}

// Encrypt the ID
if (!Yii::$app->user->isGuest) {
    $encryptedId = Yii::$app->security->encryptByKey(
        Yii::$app->user->identity->id, // ID to encrypt
        Yii::$app->params['encryptionKey'] ?? Yii::$app->security->generateRandomString(32)
    );
    // URL encode for safe transmission
    $safeEncryptedId = urlencode(base64_encode($encryptedId));
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://kit.fontawesome.com/03646e0eff.js" crossorigin="anonymous"></script>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
            ],
        ]);
        // $menuItems = [
        //     ['label' => 'Home', 'url' => ['/site/index']],
        //     // ['label' => 'About', 'url' => ['/site/about']],
        //     // ['label' => 'Contact', 'url' => ['/site/contact']],
        //     ['label' => 'Workflow', 'url' => ['/workflow']],
        //     ['label' => 'Gii', 'url' => ['/gii']],
        //     [
        //         'label' => "Extra's",
        //         'items' => [
        //             ['label' => 'Signatures', 'url' => ['/signature/index']],
        //             ['label' => 'Posts', 'url' => ['/posts/index']],
        //             // Add more dropdown items here as needed
        //         ],
        //     ],
        // ];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        }

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
            'items' => $menuItems,
        ]);
        // echo Notifications::widget();
        // if (Yii::$app->user->isGuest) {
        //     echo Html::tag('div', Html::a('Login', ['/site/login'], ['class' => ['btn btn-link login text-decoration-none']]), ['class' => ['d-flex']]);
        // } else {
        //     echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
        //         . Html::submitButton(
        //             '<i class="fa-solid fa-user-astronaut"></i> Logout (' . Yii::$app->user->identity->username . ')',
        //             ['class' => 'btn btn-link logout text-decoration-none']
        //         )
        //         . Html::endForm();
        // }

        if (Yii::$app->user->isGuest) {
            echo Html::tag(
                'div',
                Html::a('Login', ['/site/login'], ['class' => ['btn btn-link login text-decoration-none']]),
                ['class' => ['d-flex']]
            );
        } else {
            echo Html::beginTag(
                'div',
                ['class' => 'd-flex items-center justify-content-center gap-2']
            );

            // echo \common\widgets\NotificationDropdown::widget();

            echo Html::beginTag('div', ['class' => 'dropdown d-flex']);

            // Dropdown toggle button
            echo Html::button(
                '<i class="fa-solid fa-user-astronaut"></i> ' . Html::encode(Yii::$app->user->identity->username),
                [
                    'class' => 'btn btn-link text-decoration-none dropdown-toggle',
                    'data-bs-toggle' => 'dropdown',
                    'aria-expanded' => 'false'
                ]
            );

            // Dropdown menu items
            echo Dropdown::widget([
                'items' => [
                    [
                        'label' => '<i class="fa-solid fa-user"></i> Profile',
                        'url' => ['/user/profile', 'id' => $safeEncryptedId],
                        'encode' => false
                    ],
                    [
                        'label' => '<i class="fa-solid fa-right-from-bracket"></i> Logout',
                        'url' => ['/site/logout'],
                        'linkOptions' => [
                            'data-method' => 'post',
                            'class' => 'dropdown-item'
                        ],
                        'encode' => false
                    ],
                ],
                'options' => [
                    'class' => 'dropdown-menu dropdown-menu-end'
                ],
                'encodeLabels' => false
            ]);

            echo Html::endTag('div');
            echo Html::endTag('div');
        }

        NavBar::end();
        ?>
    </header>

    <main role="main" class="flex-shrink-0">
        <div class="container">
            <?php \dominus77\sweetalert2\Alert::widget(['useSessionFlash' => true]); ?>

            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted">
        <div class="container">
            <p class="float-start">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
            <p class="float-end"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage();
