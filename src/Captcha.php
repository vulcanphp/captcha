<?php

namespace VulcanPhp\Captcha;

use Exception;

class Captcha
{
    const TYPE_ALPHANUMERIC     = 1,
        TYPE_MATHEMATICAL       = 2,
        DIFFICULTY_EASY         = 1,
        DIFFICULTY_MEDIUM       = 2,
        DIFFICULTY_HARD         = 3;

    public $id, $type, $difficulty;

    public function __construct(
        string $id,
        int $type = self::TYPE_ALPHANUMERIC,
        int $difficulty = self::DIFFICULTY_HARD
    ) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->id           = $id;
        $this->type         = $type;
        $this->difficulty   = $difficulty;
    }

    public static function render(
        string $id,
        int $type = self::TYPE_ALPHANUMERIC,
        int $difficulty = self::DIFFICULTY_HARD,
        int $total = 5
    ): void {
        $captcha = new self($id, $type, $difficulty);
        $images  = [];
        $phrases = [];

        for ($i = 1; $i <= $total; $i++) {
            $phrases[]  = ($phrase = $captcha->generatePhrase());
            $images[]   = CaptchaBuilder::generate($phrase, 160, 50);
        }

        echo $captcha
            ->setPhrases($phrases)
            ->output($images);
    }

    public static function validate(
        string $id,
        int $type = self::TYPE_ALPHANUMERIC
    ): bool {
        $captcha = new self($id, $type);

        return $captcha->compare(
            intval($_REQUEST["_captcha-$id-serial"] ?? 0),
            trim($_REQUEST["_captcha-$id-input"] ?? '')
        );
    }

    protected function generatePhrase(): string
    {
        $phrase = '';
        if ($this->type == self::TYPE_ALPHANUMERIC) {
            $chars = str_split(
                sprintf(
                    '%s%s%s',
                    $this->difficulty >= self::DIFFICULTY_EASY ? 'abcdefghijklmnopqrstuvwxyz' : '',
                    $this->difficulty >= self::DIFFICULTY_MEDIUM ? '123456789' : '',
                    $this->difficulty >= self::DIFFICULTY_HARD ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : ''
                )
            );

            for ($i = 0; $i < 5; $i++) {
                $phrase .= $chars[array_rand($chars)];
            }
        } elseif ($this->type == self::TYPE_MATHEMATICAL) {
            $phrase = sprintf(
                '%s%s%s',
                ($f = rand(4, 9)),
                ['+', '-', '*'][rand(0, 2)],
                rand(1, ($f - 1))
            );
        } else {
            throw new Exception('Invalid Captcha Type');
        }

        return $phrase;
    }

    protected function setPhrases(array $phrases): self
    {
        $_SESSION['captcha-' . $this->id] = $phrases;
        return $this;
    }

    protected function output(array $images): string
    {
        extract(['captcha' => $this, 'images' => $images]);
        ob_start();

        include __DIR__ . '/resources/output.php';

        return ob_get_clean();
    }

    protected function compare(int $serial, string $phrase): bool
    {
        $phrase2 = ($_SESSION['captcha-' . $this->id][$serial] ?? null);

        return $this->type == self::TYPE_MATHEMATICAL ?
            $this->isMathEqual($phrase2, $phrase) :
            $phrase2 === $phrase;
    }

    protected function isMathEqual(string $text, int $phrase): bool
    {
        return eval('return ' . $text . ';') == $phrase;
    }
}
