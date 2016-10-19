<?php

namespace Skychf\Captcha;

/**
 * Builds a new captcha image
 *
 * @author Skychf <skychf@qq.com>
 */
class CaptchaBuilder
{
    /**
     * 画布宽度
     * @var integer
     */
    protected $width = 105;

    /**
     * 画布高度
     * @var integer
     */
    protected $height = 39;

    /**
     * 长度
     * @var integer
     */
    protected $length = 4;

    /**
     * 字体大小
     * @var integer
     */
    protected $fontSize = 18;

    /**
     * 显示类别
     * @var integer
     */
    protected $wordType = 2;

    /**
     * 滤镜效果
     * @var integer
     */
    protected $filterType = 4;

    /**
     * 画布资源
     */
    protected $im = null;

    /**
     * 字体颜色
     * @var array
     */
    protected $fontColor = [];

    /**
     * 生成的字符
     * @var string
     */
    protected $chars = '';

    /**
     * 设置现布宽度
     * @author skychf skychf@qq.com
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * 设置画布度度
     * @author skychf skychf@qq.com
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * 设置显示个数
     * @author skychf skychf@qq.com
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * 设置字体大小
     * @author skychf skychf@qq.com
     */
    public function setfontSize($fontSize)
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * 设置显示方式
     * @author skychf skychf@qq.com
     */
    public function setWordType($wordType)
    {
        $this->wordType = $wordType;
        return $this;
    }

    /**
     * 设置显示效果
     * @author skychf skychf@qq.com
     */
    public function setFilterType($type)
    {
        $this->filterType = $type;
        return $this;
    }
    /**
     * 创建一个画布资源
     * @author skychf skychf@qq.com
     */
    protected function createIm()
    {
        $this->im = imagecreate($this->width, $this->height);
        imagecolorallocate($this->im, 255, 255, 255);

        $this->fontColor = [
            imagecolorallocate($this->im, 0x15, 0x15, 0x15),
            imagecolorallocate($this->im, 0x95, 0x1e, 0x04),
            imagecolorallocate($this->im, 0x93, 0x14, 0xa9),
            imagecolorallocate($this->im, 0x12, 0x81, 0x0a),
            imagecolorallocate($this->im, 0x06, 0x3a, 0xd5)
        ];

        $this->createFilter();
    }

    private function createFilter()
    {
        $lineColor = imagecolorallocate($this->im, 0xda, 0xd9, 0xd1);
        for ($i = 3; $i <= $this->height -3; $i = $i + 3) { // 横线
            imageline($this->im, 2, $i, $this->width -2, $i, $lineColor);
        }

        for ($i = 2; $i <= 100; $i = $i + 6) { // 竖线
            imageline($this->im, $i, 0, $i + 8, $this->height, $lineColor);
        }

        if ($this->filterType == 4) { // 边框
            $bordercolor = imagecolorallocate($this->im, 0xcd, 0xcd, 0xcd);
            imagerectangle($this->im, 0, 0, $this->width - 1, $this->height - 1, $bordercolor);
        }

        $this->createText();
    }

    /**
     * 获取验证码生成的字符串
     * @author skychf skychf@qq.com
     */
    public function getChars()
    {
        if ($this->chars) return $this->chars;
        for ($i = 0; $i < $this->length; $i++) {
            if ($this->wordType == 1) {
                $char = chr(mt_rand(48, 57));
            } else {
                $char = chr(mt_rand(65, 90));
            }
            $this->chars .= $char;
        }
        return strtoupper($this->chars);
    }

    /**
     * 获取随机字符
     * @author skychf skychf@qq.com
     * @return String
     */
    private function createText()
    {
        $chars = $this->getChars();
        for ($i = 0; $i < $this->length; $i++) {
            $angle = mt_rand(0, 15);
            $y = $this->height - ($this->fontSize/2);
            $x = ($i == 0) ? 10 : ($i * ceil($this->width/$this->length));
            $fontColor = $this->fontColor[mt_rand(0,4)];
            $fontFile = __DIR__ . '/Fonts/texb.ttf';
            $fontBox = imagettfbbox($this->fontSize, 0, $fontFile, $chars[$i]);
            imagettftext($this->im, $this->fontSize, $angle, $x, $y, $fontColor, $fontFile, $chars[$i]);
        }
    }

    /**
     * 获取图片
     * @author skychf skychf@qq.com
     */
    private function get()
    {
        ob_start();
        $this->output(false);
        return ob_get_clean();
    }

    /**
     * 输出图片
     * @author skychf skychf@qq.com
     */
    public function output($flag = true)
    {
        $this->createIm();
        switch ($this->filterType) {
            case '1':
                imagefilter($this->im, IMG_FILTER_NEGATE);
                break;
            case '2':
                imagefilter($this->im, IMG_FILTER_EMBOSS);
                break;
            case '3':
                imagefilter($this->im, IMG_FILTER_EDGEDETECT);
            default:
                break;
        }

        header("Pragma:no-cache");
        header("Cache-Control:no-cache");
        header("Expires:0");

        if ($flag) {
            header("Content-type:image/jpeg");
        }
        imagejpeg($this->im);
        imagedestroy($this->im);
    }

    /**
     * base64 图片
     * @author skychf skychf@qq.com
     */
    public function inline()
    {
        return 'data:image/jpeg;base64,' . base64_encode($this->get());
    }
}