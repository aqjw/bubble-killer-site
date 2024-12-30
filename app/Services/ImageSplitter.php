<?php

namespace App\Services;

class ImageSplitter
{
    public static function loadImage(string $path)
    {
        $imageType = exif_imagetype($path);
        return match ($imageType) {
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
        };
    }

    public function split(string $path, int $angle, int $x, int $y): array
    {
        // Load the image
        $sourceImage = $this->loadImage($path);

        if (! $sourceImage) {
            throw new \RuntimeException('Failed to load image from path: ' . $path);
        }

        // Get dimensions
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        // Create two blank images for parts
        $part1 = imagecreatetruecolor($width, $height);
        $part2 = imagecreatetruecolor($width, $height);

        // Collect colors along the split line
        $dominantColorAbove = $this->getDominantSplitLineColor($sourceImage, $angle, $x, $y, true);
        $dominantColorBelow = $this->getDominantSplitLineColor($sourceImage, $angle, $x, $y, false);

        // Fill the images with the dominant colors
        imagefill($part1, 0, 0, $dominantColorAbove);
        imagefill($part2, 0, 0, $dominantColorBelow);

        // Loop through pixels and split with an offset based on angle
        for ($px = 0; $px < $width; $px++) {
            // Если угол близок к вертикальному (90 или 270 градусов)
            if (abs($angle - 90) < 1 || abs($angle - 270) < 1) {
                // Разделяем по вертикальной линии (x - координата разделения)
                for ($py = 0; $py < $height; $py++) {
                    $color = imagecolorat($sourceImage, $px, $py);

                    if ($px < $x) {
                        imagesetpixel($part1, $px, $py, $color);
                    } else {
                        imagesetpixel($part2, $px, $py, $color);
                    }
                }
            } else {
                // Для наклонного разделения
                $offset = (int) (($px - $x) * tan(deg2rad($angle)) + $y);

                for ($py = 0; $py < $height; $py++) {
                    $color = imagecolorat($sourceImage, $px, $py);

                    if ($py < $offset) {
                        imagesetpixel($part1, $px, $py, $color);
                    } else {
                        imagesetpixel($part2, $px, $py, $color);
                    }
                }
            }
        }


        // Remove whitespace from parts
        $part1 = $this->removeWhitespace($part1);
        $part2 = $this->removeWhitespace($part2);

        // Ensure both parts have valid dimensions
        if (imagesx($part1) <= 0 || imagesy($part1) <= 0) {
            throw new \RuntimeException('Part 1 has invalid dimensions after trimming.');
        }

        if (imagesx($part2) <= 0 || imagesy($part2) <= 0) {
            throw new \RuntimeException('Part 2 has invalid dimensions after trimming.');
        }

        // Define output paths
        $part1Path = tempnam(sys_get_temp_dir(), 'part1') . '.png';
        $part2Path = tempnam(sys_get_temp_dir(), 'part2') . '.png';

        // Save the parts
        if (! imagepng($part1, $part1Path)) {
            throw new \RuntimeException('Failed to save part1 image');
        }

        if (! imagepng($part2, $part2Path)) {
            throw new \RuntimeException('Failed to save part2 image');
        }

        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($part1);
        imagedestroy($part2);

        return [$part1Path, $part2Path];
    }

    private function removeWhitespace($img, $tolerance = 3)
    {
        // Проверяем, является ли изображение true color, если нет - конвертируем
        if (! imageistruecolor($img)) {
            $img = $this->convertToTrueColorWithAlpha($img);
        }

        // Получаем ширину, высоту изображения
        $width = imagesx($img);
        $height = imagesy($img);

        // Считываем пиксели изображения в массив
        $pixels = [];
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $pixels[$y][$x] = imagecolorat($img, $x, $y);
            }
        }

        // Рассчитываем стандартное отклонение по строкам
        $rowStd = array_map(function ($row) {
            $values = array_map(function ($pixel) {
                return ($pixel >> 16) & 0xFF; // Извлекаем красный канал
            }, $row);
            return $this->calculateStandardDeviation($values);
        }, $pixels);

        // Рассчитываем стандартное отклонение по столбцам
        $colStd = [];
        for ($x = 0; $x < $width; $x++) {
            $colValues = [];
            for ($y = 0; $y < $height; $y++) {
                $colValues[] = ($pixels[$y][$x] >> 16) & 0xFF; // Извлекаем красный канал
            }
            $colStd[] = $this->calculateStandardDeviation($colValues);
        }

        // Определяем строки и столбцы с вариативностью цвета
        $nonUniformRows = array_map(fn ($std) => $std > $tolerance, $rowStd);
        $nonUniformCols = array_map(fn ($std) => $std > $tolerance, $colStd);

        // Проверяем, есть ли строки и столбцы с вариативностью
        if (! in_array(true, $nonUniformRows) || ! in_array(true, $nonUniformCols)) {
            // Если нет вариативных строк или столбцов, возвращаем оригинальное изображение
            return $img;
        }

        // Находим границы для обрезки
        $upper = array_search(true, $nonUniformRows);
        $lower = count($nonUniformRows) - array_search(true, array_reverse($nonUniformRows)) - 1;
        $left = array_search(true, $nonUniformCols);
        $right = count($nonUniformCols) - array_search(true, array_reverse($nonUniformCols)) - 1;

        // Обрезаем изображение по найденным границам
        $croppedImg = imagecrop($img, [
            'x' => $left,
            'y' => $upper,
            'width' => $right - $left + 1,
            'height' => $lower - $upper + 1
        ]);

        if ($croppedImg !== false) {
            // Сохраняем альфа-канал у обрезанного изображения
            imagealphablending($croppedImg, false);
            imagesavealpha($croppedImg, true);
            return $croppedImg;
        }

        return $img;
    }

    private function getDominantSplitLineColor($image, int $angle, int $x, int $y, bool $above): int
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $colorCounts = [];

        for ($px = 0; $px < $width; $px++) {
            $offset = (int) (($px - $x) * tan(deg2rad($angle)) + $y);
            $py = $above ? max(0, $offset - 1) : min($height - 1, $offset + 1);

            // Проверяем, что координаты в пределах изображения
            if ($py >= 0 && $py < $height) {
                $color = imagecolorat($image, $px, $py);
                if (! isset($colorCounts[$color])) {
                    $colorCounts[$color] = 0;
                }
                $colorCounts[$color]++;
            }
        }

        // Находим цвет с максимальным количеством пикселей
        arsort($colorCounts);
        return array_key_first($colorCounts);
    }

    private function convertToTrueColorWithAlpha($img)
    {
        // Создаем пустое true color изображение с поддержкой прозрачности
        $width = imagesx($img);
        $height = imagesy($img);
        $trueColorImg = imagecreatetruecolor($width, $height);

        // Включаем прозрачность и альфа-канал
        imagealphablending($trueColorImg, false);
        imagesavealpha($trueColorImg, true);

        // Копируем оригинальное изображение на true color холст с альфа-каналом
        imagecopy($trueColorImg, $img, 0, 0, 0, 0, $width, $height);

        return $trueColorImg;
    }

    private function calculateStandardDeviation(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn ($v) => pow($v - $mean, 2), $values)) / count($values);
        return sqrt($variance);
    }
}
