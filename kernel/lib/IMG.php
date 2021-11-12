<?php

class IMG {
	const waterMarkTopLeft = 1, waterMarkTopRight = 2, waterMarkBottomLeft = 3, waterMarkBottomRight = 4, waterMarkCenter = 5;

	private function __construct() {}

	public static function resize($from, $to, $new_width, $new_height) {
		return self::_resize('usual', $from, $to, $new_width, $new_height);
	}
	public static function resizeIdiot($from, $to, $new_width, $new_height) {
		return self::_resize('idiot', $from, $to, $new_width, $new_height);
	}
	public static function resizeCrop($from, $to, $new_width, $new_height = null) {
		if ($new_height === null) $new_height = $new_width;
		return self::_resize('crop', $from, $to, $new_width, $new_height);
	}
	public static function resizeAmbilight($from, $to, $new_width, $new_height, $padding = 5) {
		return self::_resize('ambilight', $from, $to, $new_width, $new_height, array('padding' => $padding));
	}

	public static function _resize($method, $from, $to, $new_width, $new_height, $params = array()) {
		$from = preg_replace('/\0/uis', '', $from);
		$to = preg_replace('/\0/uis', '', $to);
		$imageinfo = @getimagesize($from);
		if (!$imageinfo) return false;

		$src_img = 0;
		$src_width = $imageinfo[0];
		$src_height = $imageinfo[1];
		$format = $imageinfo[2];
		$mime = $imageinfo['mime'];

		switch ($format) {
			case IMAGETYPE_JPEG:
				$src_img = @imagecreatefromjpeg($from);
			break;
			case IMAGETYPE_PNG:
				$src_img = @imagecreatefrompng($from);
			break;
			default:
				return false;
			break;
		}
		if (!$src_img) return false;

		$dst_img = null;
		switch ($method) {
			case 'usual':
				if ($new_width == $new_height) {
					if ($src_width > $new_width && $src_height > $new_height) {
						if ($src_width == $src_height) {
							$dst_width = $new_width;
							$dst_height = $new_height;
						}
						elseif ($src_width > $src_height) {
							$dst_width = $new_width;
							$dst_height = intval(((float)$dst_width / (float)$src_width) * $src_height);
						}
						else {
							$dst_height = $new_height;
							$dst_width = intval(((float)$dst_height / (float)$src_height) * $src_width);
						}
					}
					elseif ($src_width > $new_width) {
						$dst_width = $new_width;
						$dst_height = intval(((float)$dst_width / (float)$src_width) * $src_height);
					}
					elseif ($src_height > $new_height) {
						$dst_height = $new_height;
						$dst_width = intval(((float)$dst_height / (float)$src_height) * $src_width);
					}
					else {
						$dst_width = $src_width;
						$dst_height = $src_height;
					}
				}
				elseif ($new_width > $new_height) {
					if ($src_width > $new_width && $src_height > $new_height) {
						if ($src_width > $src_height) {
							$tmp_width = $new_width;
							$tmp_height = intval(((float)$tmp_width / (float)$src_width) * $src_height);
							if ($tmp_height > $new_height) {
								$dst_height = $new_height;
								$dst_width = intval(((float)$dst_height / (float)$tmp_height) * $tmp_width);
							} else {
								$dst_width = $tmp_width;
								$dst_height = $tmp_height;
							}
						}
						else {
							$tmp_height = $new_height;
							$tmp_width = intval(((float)$tmp_height / (float)$src_height) * $src_width);
							if ($tmp_width > $new_width) {
								$dst_width = $new_width;
								$dst_height = intval(((float)$dst_width / (float)$tmp_width) * $tmp_height);
							} else {
								$dst_height = $tmp_height;
								$dst_width = $tmp_width;
							}
						}
					}
					elseif ($src_width > $new_width) {
						$dst_width = $new_width;
						$dst_height = intval(((float)$dst_width / (float)$src_width) * $src_height);
					}
					elseif ($src_height > $new_height) {
						$dst_height = $new_height;
						$dst_width = intval(((float)$dst_height / (float)$src_height) * $src_width);
					}
					else {
						$dst_width = $src_width;
						$dst_height = $src_height;
					}
				}
				elseif ($new_width < $new_height) {
					if ($src_width > $new_width && $src_height > $new_height) {
						if ($src_width >= $src_height) {
							$tmp_width = $new_width;
							$tmp_height = intval(((float)$tmp_width / (float)$src_width) * $src_height);
							if ($tmp_height > $new_height) {
								$dst_height = $new_height;
								$dst_width = intval(((float)$dst_height / (float)$tmp_height) * $tmp_width);
							} else {
								$dst_width = $tmp_width;
								$dst_height = $tmp_height;
							}
						}
						else {
							$tmp_height = $new_height;
							$tmp_width = intval(((float)$tmp_height / (float)$src_height) * $src_width);
							if ($tmp_width > $new_width) {
								$dst_width = $new_width;
								$dst_height = intval(((float)$dst_width / (float)$tmp_width) * $tmp_height);
							} else {
								$dst_height = $tmp_height;
								$dst_width = $tmp_width;
							}
						}
					}
					elseif ($src_width > $new_width) {
						$dst_width = $new_width;
						$dst_height = intval(((float)$dst_width / (float)$src_width) * $src_height);
					}
					elseif ($src_height > $new_height) {
						$dst_height = $new_height;
						$dst_width = intval(((float)$dst_height / (float)$src_height) * $src_width);
					}
					else {
						$dst_width = $src_width;
						$dst_height = $src_height;
					}
				}

				$dst_img = imagecreatetruecolor($dst_width, $dst_height);
				$white = imagecolorallocate($dst_img, 255, 255, 255);
				imagefilledrectangle($dst_img, 0, 0, $dst_width - 1, $dst_height - 1, $white);
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
			break;
			case 'idiot':
				$dst_width = $new_width;
				$dst_height = $new_height;

				$dst_img = imagecreatetruecolor($dst_width, $dst_height);
				$white = imagecolorallocate($dst_img, 255, 255, 255);
				imagefilledrectangle($dst_img, 0, 0, $dst_width - 1, $dst_height - 1, $white);
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
			break;
			case 'crop':
				$x = 0;
				$y = 0;
				if ($new_width == $new_height) {
					if ($src_width >= $src_height) {
						$dst_width = $src_height;
						$dst_height = $src_height;
						$x = intval(($src_width - $src_height) / 2);
					}
					else {
						$dst_width = $src_width;
						$dst_height = $src_width;
						$y = intval(($src_height - $src_width) / 4);
					}
				}
				elseif ($new_width > $new_height) {
					if ($src_width > $src_height) {
						$tmp_height = $src_height;
						$tmp_width = intval(((float)$new_width / (float)$new_height) * $src_height);
						if ($tmp_width > $src_width) {
							$dst_width = $src_width;
							$dst_height = intval(((float)$new_height / (float)$new_width) * $src_width);
							$y = intval(($src_height - $dst_height) / 2);
						} else {
							$dst_height = $tmp_height;
							$dst_width = $tmp_width;
							$x = intval(($src_width - $dst_width) / 2);
						}
					}
					else {
						$dst_width = $src_width;
						$dst_height = intval(((float)$new_height / (float)$new_width) * $src_width);
						$y = intval(($src_height - $dst_height) / 2);
					}
				}
				elseif ($new_width < $new_height) {
					if ($src_height > $src_width) {
						$tmp_width = $src_width;
						$tmp_height = intval(((float)$new_height / (float)$new_width) * $src_width);
						if ($tmp_height > $src_height) {
							$dst_height = $src_height;
							$dst_width = intval(((float)$new_width / (float)$new_height) * $src_height);
							$y = intval(($src_height - $dst_height) / 2);
						} else {
							$dst_width = $tmp_width;
							$dst_height = $tmp_height;
							$y = intval(($src_height - $dst_height) / 2);
						}
					}
					else {
						$dst_height = $src_height;
						$dst_width = intval(((float)$new_width / (float)$new_height) * $src_height);
						$x = intval(($src_width - $dst_width) / 2);
					}
				}

				$dst_img = imagecreatetruecolor($new_width, $new_height);
				$white = imagecolorallocate($dst_img, 255, 255, 255);
				imagefilledrectangle($dst_img, 0, 0, $new_width - 1, $new_height - 1, $white);
				imagecopyresampled($dst_img, $src_img, 0, 0, $x, $y, $new_width, $new_height, $dst_width, $dst_height);
			break;
			case 'ambilight':
				$padding = @$params['padding'] > 0 ? $params['padding'] : 5;
				$dst_img = imagecreatetruecolor($new_width, $new_height);
				imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $src_width, $src_height);
				for ($i = 0; $i <= 100; $i++) {
					imagefilter($dst_img, IMG_FILTER_SMOOTH, 6);
				}
				$ratio = min( ($new_width - 2 * $padding) / $src_width , ($new_height - 2 * $padding) / $src_height );
				$dst_width = $ratio * $src_width;
				$dst_height = $ratio * $src_height;
				if ( ($dst_width >= $src_width) || ($dst_height >= $src_height) ) {
					$dst_width = $src_width;
					$dst_height = $src_height;
				}
				imagecopyresampled($dst_img, $src_img, round(($new_width-$dst_width)/2), round(($new_height-$dst_height)/2), 0, 0, $dst_width, $dst_height, $src_width, $src_height);
			break;
			default:
				return false;
			break;
		}
		if (!$dst_img) return false;

		switch ($format) {
			case IMAGETYPE_JPEG:
				imagejpeg($dst_img, $to, 85);
			break;
			case IMAGETYPE_PNG:
				imagepng($dst_img, $to, 9);
			break;
		}

		imagedestroy($src_img);
		imagedestroy($dst_img);

		return true;
	}
	public static function title($file, $text, $jpvl = false) {
		$file = preg_replace('/\0/uis', '', $file);
		if (!$text) return FALSE;

		$file_vars = getimagesize($file);
		if (!$file_vars) return FALSE;
		$file_width  = $file_vars[0];
		$file_height = $file_vars[1];
		$file_type   = $file_vars[2];
		if (($file_type != IMAGETYPE_JPEG) and ($file_type != IMAGETYPE_PNG)) return FALSE;
		switch ($file_type) {
			case IMAGETYPE_JPEG: $file_image = imagecreatefromjpeg($file); break;
			case IMAGETYPE_PNG: $file_image = imagecreatefrompng($file); break;
			default: return FALSE; break;
		}
		if (!$file_image || !$file_width || !$file_height) return FALSE;

		imagealphablending($file_image, true);

		$size = 11;
		$margin = $jpvl == 1 ? 15 : 8;
		$k1 = 0.3;
		$k2 = 1.4;
		$font = KERNEL_DIR . 'fonts/arial.ttf';
		$fg_color = imagecolorallocatealpha($file_image, 0x00, 0x00, 0x00, 0);
		$bg_color = imagecolorallocatealpha($file_image, 0xFF, 0xFF, 0xFF, $jpvl ? 0 : 30);
		$box = imageftbbox($size, 0, $font, $text);
		$w = abs($box[4] - $box[6]);
		$h = abs($box[1] - $box[7]);
		imagefilledrectangle($file_image, 0, $file_height - $h - $margin * 2 - round($h * $k1 * $k2), $file_width, $file_height, $bg_color);
		imagefttext($file_image, $size, 0, ($file_width - $w) / 2, $file_height - $margin - round($h * $k1), $fg_color, $font, $text);

		switch ($file_type) {
			case IMAGETYPE_JPEG: $result = imagejpeg($file_image, $file, 90); break;
			case IMAGETYPE_PNG: $result = imagepng($file_image, $file, 9); break;
			default: return FALSE; break;
		}
		if (!$result) return FALSE;

		imagedestroy($file_image);

		return TRUE;
	}
	public static function rotateLeft($file) {
		$file = preg_replace('/\0/uis', '', $file);

		$file_vars = getimagesize($file);
		if (!$file_vars) return FALSE;
		$file_width  = $file_vars[0];
		$file_height = $file_vars[1];
		$file_type   = $file_vars[2];
		if (($file_type != IMAGETYPE_JPEG) and ($file_type != IMAGETYPE_PNG)) return FALSE;
		switch ($file_type) {
			case IMAGETYPE_JPEG: $file_image = imagecreatefromjpeg($file); break;
			case IMAGETYPE_PNG: $file_image = imagecreatefrompng($file); break;
			default: return FALSE; break;
		}
		if (!$file_image || !$file_width || !$file_height) return FALSE;

		$file_image = @imagerotate($file_image, 90, 0);

		switch ($file_type) {
			case IMAGETYPE_JPEG: $result = imagejpeg($file_image, $file, 85); break;
			case IMAGETYPE_PNG: $result = imagepng($file_image, $file, 9); break;
			default: return FALSE; break;
		}
		if (!$result) return FALSE;

		imagedestroy($file_image);

		return TRUE;
	}
	public static function rotateRight($file) {
		$file = preg_replace('/\0/uis', '', $file);

		$file_vars = getimagesize($file);
		if (!$file_vars) return FALSE;
		$file_width  = $file_vars[0];
		$file_height = $file_vars[1];
		$file_type   = $file_vars[2];
		if (($file_type != IMAGETYPE_JPEG) and ($file_type != IMAGETYPE_PNG)) return FALSE;
		switch ($file_type) {
			case IMAGETYPE_JPEG: $file_image = imagecreatefromjpeg($file); break;
			case IMAGETYPE_PNG: $file_image = imagecreatefrompng($file); break;
			default: return FALSE; break;
		}
		if (!$file_image || !$file_width || !$file_height) return FALSE;

		$file_image = @imagerotate($file_image, 270, 0);

		switch ($file_type) {
			case IMAGETYPE_JPEG: $result = imagejpeg($file_image, $file, 85); break;
			case IMAGETYPE_PNG: $result = imagepng($file_image, $file, 9); break;
			default: return FALSE; break;
		}
		if (!$result) return FALSE;

		imagedestroy($file_image);

		return TRUE;
	}
	public static function watermark($file, $wm, $place = self::waterMarkBottomRight, $margin = 10) {
		$file = preg_replace('/\0/uis', '', $file);
		$wm = preg_replace('/\0/uis', '', $wm);

		$file_vars = getimagesize($file);
		if (!$file_vars) return FALSE;
		$file_width  = $file_vars[0];
		$file_height = $file_vars[1];
		$file_type   = $file_vars[2];
		if (($file_type != IMAGETYPE_JPEG) and ($file_type != IMAGETYPE_PNG)) return FALSE;
		switch ($file_type) {
			case IMAGETYPE_JPEG: $file_image = imagecreatefromjpeg($file); break;
			case IMAGETYPE_PNG: $file_image = imagecreatefrompng($file); break;
			default: return FALSE; break;
		}
		if (!$file_image || !$file_width || !$file_height) return FALSE;

		$wm_vars = getimagesize($wm);
		if (!$wm_vars) return FALSE;
		$wm_width  = $wm_vars[0];
		$wm_height = $wm_vars[1];
		$wm_type   = $wm_vars[2];
		if (($wm_type != IMAGETYPE_JPEG) and ($wm_type != IMAGETYPE_PNG)) return FALSE;
		switch ($wm_type) {
			case IMAGETYPE_JPEG: $wm_image = imagecreatefromjpeg($wm); break;
			case IMAGETYPE_PNG: $wm_image = imagecreatefrompng($wm); break;
			default: return FALSE; break;
		}
		if (!$wm_image || !$wm_width || !$wm_height) return FALSE;

		if ($place == self::waterMarkCenter) {
			if ($file_width < $wm_width || $file_height < $wm_height) return FALSE;
			imagecopyresampled($file_image, $wm_image, $file_width/2 - $wm_width/2, $file_height/2 - $wm_height/2, 0, 0, $wm_width, $wm_height, $wm_width, $wm_height);
		}
		else {
			if ($file_width <= $wm_width * 2 || $file_height <= $wm_height * 2) return FALSE;
			switch ($place) {
				case self::waterMarkTopLeft:
					imagecopyresampled($file_image, $wm_image,
						$margin, $margin, 0, 0,
						$wm_width, $wm_height, $wm_width, $wm_height
					);
				break;
				case self::waterMarkTopRight:
					imagecopyresampled($file_image, $wm_image,
						$file_width - $wm_width - $margin, $margin, 0, 0,
						$wm_width, $wm_height, $wm_width, $wm_height
					);
				break;
				case self::waterMarkBottomLeft:
					imagecopyresampled($file_image, $wm_image,
						$margin, $file_height - $wm_height - $margin, 0, 0,
						$wm_width, $wm_height, $wm_width, $wm_height
					);
				break;
				case self::waterMarkBottomRight:
					imagecopyresampled($file_image, $wm_image,
						$file_width - $wm_width - $margin, $file_height - $wm_height - $margin, 0, 0,
						$wm_width, $wm_height, $wm_width, $wm_height
					);
				break;
				default:
					return FALSE;
				break;
			}
		}

		switch ($file_type) {
			case IMAGETYPE_JPEG: $result = imagejpeg($file_image, $file, 85); break;
			case IMAGETYPE_PNG: $result = imagepng($file_image, $file, 9); break;
			default: return FALSE; break;
		}
		if (!$result) return FALSE;

		imagedestroy($file_image);
		imagedestroy($wm_image);

		return TRUE;
	}
	public static function verify($file) {
		$file = preg_replace('/\0/uis', '', $file);
		$txt = file_get_contents($file);
		if (preg_match('#&(quot|lt|gt|nbsp|amp);#i', $txt)) return false;
		elseif (preg_match("#&\#x([0-9a-f]+);#i", $txt)) return false;
		elseif (preg_match('#&\#([0-9]+);#i', $txt)) return false;
		elseif (preg_match("#([a-z]*)=([\`\'\"]*)script:#iU", $txt)) return false;
		elseif (preg_match("#([a-z]*)=([\`\'\"]*)javascript:#iU", $txt)) return false;
		elseif (preg_match("#([a-z]*)=([\'\"]*)vbscript:#iU", $txt)) return false;
		elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU", $txt)) return false;
		elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU", $txt)) return false;
		elseif (preg_match("#</*(applet|link|style|script|iframe|frame|frameset)[^>]*>#i", $txt)) return false;
		return true;
	}
}
