<?php
//https://www.beliefmedia.com.au/convert-rgb-hsv-hsl-php

namespace Ms\Core\Entity\Type;

class Color
{
	protected $r = null;

	protected $g = null;

	protected $b = null;

	/**
	 * Конструктор Color
	 *
	 * @param string $hexColor HEX представление цвета
	 *
	 * @return Color
	 */
	public function __construct (string $hexColor = null)
	{
		if (!is_null($hexColor))
		{
			$this->setFromHexString($hexColor);
		}

		return $this;
	}

	/**
	 * Устанавливает значение красного
	 *
	 * @param int $r красный
	 *
	 * @return Color
	 */
	public function setR (int $r): Color
	{
		$r = (int)$r;
		if ($r >= 0 && $r <= 255)
		{
			$this->r = $r;
		}

		return $this;
	}

	/**
	 * Возвращает значение красного
	 *
	 * @return int
	 */
	public function getR (): int
	{
		return (int)$this->r;
	}

	/**
	 * Устанавливает значение зеленого
	 *
	 * @param int $g зеленый
	 *
	 * @return Color
	 */
	public function setG (int $g): Color
	{
		$g = (int)$g;
		if ($g >= 0 && $g <= 255)
		{
			$this->g = $g;
		}

		return $this;
	}

	/**
	 * Возвращает значение зеленого
	 *
	 * @return int
	 */
	public function getG (): int
	{
		return (int)$this->g;
	}

	/**
	 * Устанавливает значение синего
	 *
	 * @param int $b синий
	 *
	 * @return Color
	 */
	public function setB (int $b): Color
	{
		$b = (int)$b;
		if ($b >= 0 && $b <= 255)
		{
			$this->b = $b;
		}

		return $this;
	}

	/**
	 * Возвращает значение синего
	 *
	 * @return int
	 */
	public function getB (): int
	{
		return (int)$this->b;
	}


	/**
	 * Устанавливает параметры объекта из формата RGB
	 *
	 * @param int $r красный канал из формата RGB (0 - 255)
	 * @param int $g зеленый канал из формата RGB (0 - 255)
	 * @param int $b синий канал из формата RGB (0 - 255)
	 *
	 * @return Color
	 */
	public function setFromRGB (int $r, int $g, int $b): Color
	{
		if (
			!(
				$r >= 0 && $r <= 255
				&& $g >= 0 && $g <= 255
				&& $b >= 0 && $b <= 255

			)
		) {
			return $this;
		}
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;

		return $this;
	}

	public function setFromRgbInt (int $rgb): Color
	{
		if ((int)$rgb > 0)
		{
			$this->setFromHexString(dechex((int)$rgb));
		}

		return $this;
	}

	public function getFormatInteger ()
	{
		return (int)(($this->r << 16) + ($this->g << 8) + $this->b);
	}

	/**
	 * Устанавливает параметры объекта из hex представления цвета
	 *
	 * @param string $hexString Строковое представление hex цвета, например '#543d2E' или '01a' (#0011aa)
	 *
	 * @return Color
	 */
	public function setFromHexString (string $hexString): Color
	{
		$color = iconv('utf8','windows-1251',$hexString);
		if ($color[0] == '#')
		{
			$color = substr($color, 1);
		}

		if (strlen($color) == 6)
		{
			list ($r, $g, $b) = [
				$color[0] . $color[1],
				$color[2] . $color[3],
				$color[4] . $color[5]
			];
		}
		elseif (strlen($color) == 3)
		{
			list ($r, $g, $b) = [
				$color[0] . $color[0],
				$color[1] . $color[1],
				$color[2] . $color[2]
			];
		}
		else
		{
			return $this;
		}

		$this->r = hexdec($r);
		$this->g = hexdec($g);
		$this->b = hexdec($b);

		return $this;
	}

	/**
	 * Возвращает значение цвета в web hex формате
	 *
	 * @param bool $bAddSharp Добавлять знак # в начало строки
	 *
	 * @return string
	 */
	public function getFormatHexString (bool $bAddSharp = true): string
	{
		$str = '';
		if ($bAddSharp)
		{
			$str .= '#';
		}

		$r = ''.dechex((int)$this->r);
		if (strlen($r) < 2)
		{
			$r = '0' . $r;
		}
		$str .= $r;
		$g = ''.dechex((int)$this->g);
		if (strlen($g) < 2)
		{
			$g = '0' . $g;
		}
		$str .= $g;
		$b = ''.dechex((int)$this->b);
		if (strlen($b) < 2)
		{
			$b = '0' . $b;
		}
		$str .= $b;
		$str = strtoupper($str);

		return $str;
	}

	/**
	 * Возвращает значение цвета в формате RGB (array['r'=>0,'g'=>0,'b'=>0])
	 *
	 * @return array
	 */
	public function getFormatRgbArray (): array
	{
		return ['r'=>(int)$this->r,'g'=>(int)$this->g,'b'=>(int)$this->b];
	}

	/**
	 * Возвращает значение цвета в формате Hsv (array['h'=>0,'s'=>0,'v'=>0])
	 *
	 * @return array
	 */
	public function getFormatHsvArray (): array
	{
		$r = ((int)$this->r / 255);
		$g = ((int)$this->g / 255);
		$b = ((int)$this->b / 255);

		$maxRGB = max($r, $g, $b);
		$minRGB = min($r, $g, $b);
		$chroma = $maxRGB - $minRGB;

		$computedv = 100 * $maxRGB;

		if ($chroma == 0)
		{
			return ['h'=>0,'s'=>0,'v'=>$computedv];
		}

		$computeds = 100 * ($chroma / $maxRGB);

		switch ($minRGB)
		{
			case $r:
				$h = 3 - (($g - $b) / $chroma);
				break;
			case $b:
				$h = 1 - (($r - $g) / $chroma);
				break;
			default: // $g == $minRGB
				$h = 5 - (($b - $r) / $chroma);
				break;
		}
		$computedh = 60 * $h;

		return ['h'=>$computedh, 's'=>$computeds, 'v'=>$computedv];
	}

	/**
	 * Возвращает значение Hue из формата HSV
	 *
	 * @return int
	 */
	public function getFormatHsvHue ()
	{
		$arHsv = $this->getFormatHsvArray ();

		return (int)$arHsv['h'];
	}

	/**
	 * Возвращает значение Sat из формата HSV
	 *
	 * @return int
	 */
	public function getFormatHsvSat ()
	{
		$arHsv = $this->getFormatHsvArray ();

		return (int)$arHsv['s'];
	}

	/**
	 * Возвращает значение Val из формата HSV
	 *
	 * @return int
	 */
	public function getFormatHsvVal ()
	{
		$arHsv = $this->getFormatHsvArray ();

		return (int)$arHsv['v'];
	}

	/**
	 * Устанавливает значение объекта из параметров формата HSV
	 *
	 * @param float $hue
	 * @param float $sat
	 * @param float $val
	 *
	 * @return Color
	 */
	public function setFromHsv (float $hue, float $sat, float $val): Color
	{
		if ((float)$hue < 0)
		{
			$hue = 0;
		}
		elseif ((float)$hue > 360)
		{
			$hue = 360;
		}
		if ((float)$sat < 0)
		{
			$sat = 0;
		}
		elseif ((float)$sat > 100)
		{
			$sat = 100;
		}
		if ((float)$val < 0)
		{
			$val = 0;
		}
		elseif ((float)$val > 100)
		{
			$val = 100;
		}

		$dS = $sat / 100.0;
		$dV = $val / 100.0;
		$dC = $dV * $dS;
		$dH = $hue / 60.0;
		$dT = $dH;

		while ($dT >= 2.0)
		{
			$dT -= 2.0;
		}
		$dX = $dC * (1 - abs($dT-1));

		switch(floor($dH)) {
			case 0:
				$dR = $dC;
				$dG = $dX;
				$dB = 0.0;
				break;
			case 1:
				$dR = $dX;
				$dG = $dC;
				$dB = 0.0;
				break;
			case 2:
				$dR = 0.0;
				$dG = $dC;
				$dB = $dX;
				break;
			case 3:
				$dR = 0.0;
				$dG = $dX;
				$dB = $dC;
				break;
			case 4:
				$dR = $dX;
				$dG = 0.0;
				$dB = $dC;
				break;
			case 5:
				$dR = $dC;
				$dG = 0.0;
				$dB = $dX;
				break;
			default:
				$dR = 0.0;
				$dG = 0.0;
				$dB = 0.0;
				break;
		}

		$dM  = $dV - $dC;
		$dR += $dM;
		$dG += $dM;
		$dB += $dM;
		$dR *= 255;
		$dG *= 255;
		$dB *= 255;

		$this->setR($dR);
		$this->setG($dG);
		$this->setB($dB);

		return $this;
	}

	/**
	 * Возвращает значение цвета в формате HSL, в виде массива: ['h'=>0, 's'=>0, 'l'=>0]
	 *
	 * @return array
	 */
	public function getFormatHslArray (): array
	{
		$r = ((int)$this->r / 255);
		$g = ((int)$this->g / 255);
		$b = ((int)$this->b / 255);

		$max = max( $r, $g, $b );
		$min = min( $r, $g, $b );

		$h = $s = $l = 0;
		$l = ( $max + $min ) / 2;
		$d = $max - $min;

		if( $d == 0 ) {
			$h = $s = 0;

		} else {

			$s = $d / ( 1 - abs( 2 * $l - 1 ) );

			switch( $max ) {
				case $r:
					$h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
					if ($b > $g) {
						$h += 360;
					}
					break;

				case $g:
					$h = 60 * ( ( $b - $r ) / $d + 2 );
					break;

				case $b:
					$h = 60 * ( ( $r - $g ) / $d + 4 );
					break;
			}
		}
		return ['h' => round($h, 2), 's' => round($s, 2), 'l' => round($l, 2)];
	}

	/**
	 * Устанавливает значение цвета из формата HSL
	 *
	 * @param float $h
	 * @param float $s
	 * @param float $l
	 *
	 * @return Color
	 */
	public function setFromHsl (float $h, float $s, float $l): Color
	{
		$c = (1 - abs(2 * $l - 1)) * $s;
		$x = $c * (1 - abs(fmod( ($h / 60), 2) - 1));
		$m = $l - ($c / 2);

		switch($h) {
			case ($h < 60):
				$r = $c;
				$g = $x;
				$b = 0;
				break;
			case ($h < 120):
				$r = $x;
				$g = $c;
				$b = 0;
				break;
			case ($h < 180):
				$r = 0;
				$g = $c;
				$b = $x;
				break;
			case ($h < 240):
				$r = 0;
				$g = $x;
				$b = $c;
				break;
			case ($h < 300):
				$r = $x;
				$g = 0;
				$b = $c;
				break;
			default:
				$r = $c;
				$g = 0;
				$b = $x;
				break;
		}

		$r = floor(($r + $m) * 255);
		$g = floor(($g + $m) * 255);
		$b = floor(($b + $m) * 255);

		$this->setR($r);
		$this->setG($g);
		$this->setB($b);

		return $this;
	}

	/**
	 * Проверяет правильность создания объекта класса
	 *
	 * @return bool
	 */
	public function isCorrect ()
	{
		return (!is_null($this->r) && !is_null($this->g) && !is_null($this->b));
	}

	/**
	 * Возвращает объект в виде RGB массива: ['r'=>0, 'g'=>0, 'b'=>0]
	 *
	 * @return array
	 */
	public function __toArray(): array
	{
		return $this->getFormatRgbArray();
	}

	/**
	 * Возвращает объект в полном формате HEX: #FFFFFF
	 *
	 * @return string
	 */
	public function __toString (): string
	{
		return $this->getFormatHexString();
	}
}