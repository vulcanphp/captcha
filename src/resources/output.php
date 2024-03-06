<?php if (!isset($_ENV['captcha_css_init']) || !$_ENV['captcha_css_init']) : ?>
    <?php $_ENV['captcha_css_init'] = true ?>
    <style>
        <?= file_get_contents(__DIR__ . '/captcha.css') ?>
    </style>
<?php endif ?>

<div id="captcha-<?= $captcha->id ?>" class="vulcan-captcha">
    <img src="<?= $images[0] ?>">
    <button type="button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path fill="currentColor" d="M10 11H7.101l.001-.009a4.956 4.956 0 0 1 .752-1.787 5.054 5.054 0 0 1 2.2-1.811c.302-.128.617-.226.938-.291a5.078 5.078 0 0 1 2.018 0 4.978 4.978 0 0 1 2.525 1.361l1.416-1.412a7.036 7.036 0 0 0-2.224-1.501 6.921 6.921 0 0 0-1.315-.408 7.079 7.079 0 0 0-2.819 0 6.94 6.94 0 0 0-1.316.409 7.04 7.04 0 0 0-3.08 2.534 6.978 6.978 0 0 0-1.054 2.505c-.028.135-.043.273-.063.41H2l4 4 4-4zm4 2h2.899l-.001.008a4.976 4.976 0 0 1-2.103 3.138 4.943 4.943 0 0 1-1.787.752 5.073 5.073 0 0 1-2.017 0 4.956 4.956 0 0 1-1.787-.752 5.072 5.072 0 0 1-.74-.61L7.05 16.95a7.032 7.032 0 0 0 2.225 1.5c.424.18.867.317 1.315.408a7.07 7.07 0 0 0 2.818 0 7.031 7.031 0 0 0 4.395-2.945 6.974 6.974 0 0 0 1.053-2.503c.027-.135.043-.273.063-.41H22l-4-4-4 4z"></path>
        </svg>
    </button>
    <input type="text" required name="_captcha-<?= $captcha->id ?>-input" placeholder="<?= $captcha->type == $captcha::TYPE_MATHEMATICAL ? 'Result ?' : 'Enter Captcha' ?>">
    <input type="hidden" name="_captcha-<?= $captcha->id ?>-serial" value="0">
</div>

<?php if (!isset($_ENV['captcha_js_init']) || !$_ENV['captcha_js_init']) : ?>
    <?php $_ENV['captcha_js_init'] = true ?>
    <script>
        <?= file_get_contents(__DIR__ . '/captcha.js') ?>
    </script>
<?php endif ?>

<script>
    vulcanCaptcha('<?= $captcha->id ?>', <?= json_encode($images) ?>);
</script>