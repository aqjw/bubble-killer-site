# mask/frame.py

from PIL import Image, ImageOps
from pathlib import Path
import os, sys
import numpy as np
from ultralytics import YOLO
import cv2

model = None  # Placeholder for the model


def predict_mask(img):
    # Convert the image to a numpy array
    img_np = np.array(img)
    h, w, _ = img_np.shape

    # Run predictions using the model
    results = model.predict(source=img_np, show=False, save=False)

    result = results[0]
    img = np.copy(result.orig_img)
    final_mask = np.zeros(img.shape[:2], np.uint8)

    # Iterate each object contour
    for ci, c in enumerate(result):
        # TODO:
        # Create contour mask
        contour = c.masks.xy.pop().astype(np.int32).reshape(-1, 1, 2)
        _ = cv2.drawContours(final_mask, [contour], -1, (255, 255, 255), cv2.FILLED)

    # Convert the mask back to a PIL image
    mask_image = Image.fromarray(final_mask)

    return mask_image


def process(input_dir, input_files, output_dir):
    files = []

    for image_name in input_files.split(','):
        image_path = f"{input_dir}/{image_name}"
        mask_path = f"{output_dir}/{image_name}"

        # Open the image
        image = Image.open(image_path).convert("RGB")

        # Generate the mask image
        result_img = predict_mask(image)

        # Save mask image
        result_img.save(mask_path, format="PNG", optimize=True)

        # Add file name to array
        files.append(image_name)

    return files


def process_folder(input_dir, input_files, output_dir):
    global model
    print(f"Processing folder: {input_dir}")

    # Убедиться, что директория для сохранения существует
    Path(output_dir).mkdir(parents=True, exist_ok=True)

    # Загрузка модели
    model_path = "/Users/antonshever/Desktop/yolo-seg-bubble-best.pt"
    model = YOLO(model_path)

    # - - -
    files = process(input_dir, input_files, output_dir)

    return files

