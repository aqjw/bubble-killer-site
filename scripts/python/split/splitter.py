# split/splitter.py

from PIL import Image, ImageOps
import numpy as np
from pathlib import Path
import os, sys
from split.divider import find_dividers
from utils.json_wrapper import get_json


def load_images(images_dir, min_dimensions):
    # Iterate over all files in the directory
    file_paths = [Path(images_dir) / filename for filename in os.listdir(images_dir)
                  if filename.lower().endswith(('.png', '.jpg', '.jpeg'))]

    # Sort file paths by numbers extracted from filenames
    file_paths.sort(key=lambda f: int(''.join(filter(str.isdigit, f.stem))))

    files_len = len(file_paths)
    print(f"Loading {files_len} images from '{images_dir}'...")

    # Initialize list of images
    images = []

    # Load images from sorted paths
    for index, path in enumerate(file_paths, start=1):
        print(f"Load Image {index}/{files_len} from '{path}'")

        with Image.open(path) as img:
            if img.height > min_dimensions[0] and img.width > min_dimensions[1]:
                images.append(img.copy())

    return images


def create_vertical_image(img_1, img_2):
    """Создает вертикальное изображение из двух изображений с одинаковой шириной."""
    # Находим изображение с меньшей шириной
    min_width = min(img_1.width, img_2.width)

    # Изменяем размер изображений, чтобы их ширина была одинаковой
    if img_1.width != min_width:
        img_1 = img_1.resize((min_width, int(img_1.height * min_width / img_1.width)), Image.Resampling.LANCZOS)
    if img_2.width != min_width:
        img_2 = img_2.resize((min_width, int(img_2.height * min_width / img_2.width)), Image.Resampling.LANCZOS)

    # Объединяем изображения вертикально
    total_height = img_1.height + img_2.height
    new_img = Image.new('RGB', (min_width, total_height))
    new_img.paste(img_1, (0, 0))
    new_img.paste(img_2, (0, img_1.height))

    return new_img


def split_image(img, dividers, min_dimensions):
    min_width, min_height = min_dimensions

    if img.width < min_width:
        return []

    parts = []
    start = 0

    for start_divider, end_divider in dividers:
        if start_divider - start >= min_height:
            # Извлекаем часть изображения между текущим `start` и `start_divider`
            part = img.crop((0, start, img.width, start_divider))
            if is_not_empty(part):
                parts.append(part)

        # Обновляем начало на конец текущего разделителя
        start = end_divider + 1

    # Добавляем последнюю часть, если она соответствует критериям
    if img.height - start >= min_height:
        part = img.crop((0, start, img.width, img.height))
        if is_not_empty(part):
            parts.append(part)

    return parts


def is_not_empty(img, threshold=5):
    """Определяет, не является ли часть изображения 'пустой'."""
    data = np.array(img)
    if np.std(data) < threshold:
        return False
    return True


def remove_image_whitespace(img, tolerance=5):
    # Конвертируем изображение в RGB
    img = img.convert("RGB")

    # Преобразуем изображение в массив numpy
    img_array = np.array(img)

    # Рассчитываем стандартное отклонение по строкам и столбцам
    row_std = np.std(img_array, axis=(1, 2))  # Отклонение по строкам
    col_std = np.std(img_array, axis=(0, 2))  # Отклонение по столбцам

    # Определяем строки и столбцы с вариативностью цвета
    non_uniform_rows = row_std > tolerance
    non_uniform_cols = col_std > tolerance

    # Проверяем, есть ли строки и столбцы с вариативностью
    if not np.any(non_uniform_rows) or not np.any(non_uniform_cols):
        # Если нет вариативных строк или столбцов, возвращаем оригинальное изображение
        return img

    # Находим границы для обрезки
    upper = np.argmax(non_uniform_rows)  # Первая строка с вариативностью
    lower = len(non_uniform_rows) - np.argmax(non_uniform_rows[::-1])  # Последняя строка
    left = np.argmax(non_uniform_cols)  # Первый столбец с вариативностью
    right = len(non_uniform_cols) - np.argmax(non_uniform_cols[::-1])  # Последний столбец

    # Обрезаем изображение по найденным границам
    return img.crop((left, upper, right, lower))


def check_image_dimensions(img, min_dimensions):
    # Получение размеров изображения
    width, height = img.size

    # Распаковка кортежа с минимальными размерами
    min_width, min_height = min_dimensions

    # Проверка размеров
    return width > min_width and height > min_height


def save_segment(segment, min_dimensions, save_path):
    # Remove whitespace
    new_segment = remove_image_whitespace(segment)

    # Check dimensions
    if check_image_dimensions(new_segment, min_dimensions):
        new_segment.save(save_path, format="PNG", optimize=False)
        return True

    return False


def process_split_images(images, output, min_dimensions):
    images_len = len(images)
    files = []

    current_img = images[0]
    saved_index = 1

    def get_file_name():
        """Формирует название изображения."""
        return f"{saved_index:03d}.png"

    def get_save_path(prefix=''):
        """Формирует путь для сохранения сегмента изображения."""
        return os.path.join(output, f"{prefix}{get_file_name()}")


    for index, next_img in enumerate(images, start=1):
        print(f"Process Image... {index}/{images_len}")

        if images_len == 1 or index == 1 or current_img is None:
            vert_img = current_img
        else:
            # Создание вертикального изображения из текущего и следующего
            vert_img = create_vertical_image(current_img, next_img)

        # Поиск разделителей на вертикальном изображении
        visual, dividers = find_dividers(vert_img, with_visual=False)

        if len(dividers) == 0 and vert_img.height > 5_000:
            visual, dividers = find_dividers(vert_img, height_threshold=0.7, with_visual=False)
            if len(dividers) == 0:
                if save_segment(vert_img, min_dimensions, get_save_path()):
                    files.append(get_file_name())
                    saved_index += 1
                    current_img = None
                continue

        if visual:
            visual.save(get_save_path('visual-'))

        # Разделение изображения на сегменты
        segments = split_image(vert_img, dividers, min_dimensions)

        current_img = segments.pop()

        for segment in segments:
            if save_segment(segment, min_dimensions, get_save_path()):
                files.append(get_file_name())
                saved_index += 1

    # Сохранение последнего сегмента
    if save_segment(current_img, min_dimensions, get_save_path()):
        files.append(get_file_name())
        saved_index += 1

    print(f"\n{images_len} images are processed and divided into {saved_index - 1} segments.")

    # files is list of saved image names
    return files


def split_images(dir_input, dir_output, min_dimensions):
    print(f"Processing folder: {dir_input}")

    # Загрузка изображений из директории
    chapter_images = load_images(dir_input, min_dimensions)

    # Убедиться, что директория для сохранения существует
    Path(dir_output).mkdir(parents=True, exist_ok=True)

    # Обработка изображений
    files = process_split_images(chapter_images, dir_output, min_dimensions)

    return files


# def process_split_list_images(images, images_path, output_path, min_dimensions):
#     files = []

#     for image_name in images:
#         image_path = images_path / image_name
#         img = Image.open(image_path)

#         # Поиск разделителей на вертикальном изображении
#         _, dividers = find_dividers(img, with_visual=False)

#         if len(dividers) == 0:
#             save_path = output_path / image_name
#             if save_segment(img, min_dimensions, save_path):
#                 files.append(image_name)
#             continue

#         # Разделение изображения на сегменты
#         segments = split_image(img, dividers, min_dimensions)

#         # Iterate through segments and add index to image name
#         for index, segment in enumerate(segments, start=1):
#             image_name = Path(image_name)
#             # Add index to image name before extension
#             indexed_image_name = f"{image_name.stem}-{index}{image_name.suffix}"
#             save_path = output_path / indexed_image_name

#             # Save segment and append file name if successful
#             if save_segment(segment, min_dimensions, save_path):
#                 files.append(indexed_image_name)

#     return files


# def split_list_images(images_dir, input_files, output_dir, min_dimensions):
#     print(f"Processing folder: {images_dir}")

#     #
#     images = input_files.split(',')
#     images_path = Path(images_dir)
#     output_path = Path(output_dir)

#     # Убедиться, что директория для сохранения существует
#     output_path.mkdir(parents=True, exist_ok=True)

#     # Обработка изображений
#     files = process_split_list_images(images, images_path, output_path, min_dimensions)

#     return files

