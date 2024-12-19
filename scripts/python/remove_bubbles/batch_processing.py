# remove_bubbles/batch_processing.py

from pathlib import Path

import cv2
import numpy as np
from PIL import Image

from iopaint.model.utils import torch_gc
from iopaint.model_manager import ModelManager
from iopaint.schema import InpaintRequest


def batch_inpaint(
    model: str,
    device,
    images_path: Path,
    masks_path: Path,
    input_files: list,
    output_path: Path
):
    output_path.mkdir(parents=True, exist_ok=True)

    files = []

    inpaint_request = InpaintRequest()
    model_manager = ModelManager(name=model, device=device)

    for image_name in input_files:
        image_path = images_path / image_name
        mask_path = masks_path / image_name
        clear_path = output_path / image_name

        if not image_path.exists() or not mask_path.exists():
            continue

        img = np.array(Image.open(image_path).convert("RGB"))
        mask = np.array(Image.open(mask_path).convert("L"))

        mask[mask >= 127] = 255
        mask[mask < 127] = 0

        # bgr
        inpaint_result = model_manager(img, mask, inpaint_request)
        inpaint_result = cv2.cvtColor(inpaint_result, cv2.COLOR_BGR2RGB)

        result_img = Image.fromarray(inpaint_result)

        # Save mask image
        result_img.save(clear_path, format="PNG", optimize=False)

        # Add file name to array
        files.append(image_name)

        torch_gc()

    return files
