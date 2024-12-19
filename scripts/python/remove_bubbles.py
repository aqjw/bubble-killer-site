# remove_bubbles.py

import argparse
import os
import torch
from pathlib import Path

from utils.json_wrapper import get_json
from remove_bubbles.batch_processing import batch_inpaint
#
from iopaint.download import cli_download_model, scan_models

def parse_arguments():
    """
    Parses command-line arguments.
    """
    parser = argparse.ArgumentParser(description="Process manga chapters.")
    parser.add_argument('--input_images', type=str, required=True, help="Path to input images folder.")
    parser.add_argument('--input_masks', type=str, required=True, help="Path to input masks folder.")
    parser.add_argument('--files', type=str, required=True, help="Input files.")
    parser.add_argument('--output', type=str, required=True, help="Path to output folder.")
    return parser.parse_args()


def process(images_dir, masks_dir, input_files, output_dir):
    # lama,ldm,zits,mat,fcf,sd1.5,anything4,realisticVision1.4,cv2,manga,sd2,paint_by_example,instruct_pix2pix
    model = "lama"
    device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
    images_path = Path(images_dir)
    masks_path = Path(masks_dir)
    output_path = Path(output_dir)
    files_list = input_files.split(',')

    scanned_models = scan_models()
    if model not in [it.name for it in scanned_models]:
        cli_download_model(model)

    files = batch_inpaint(model, device, images_path, masks_path, files_list, output_path)

    return files


if __name__ == "__main__":
    # Parse arguments
    args = parse_arguments()

    # Extract arguments
    images_dir = args.input_images
    masks_dir = args.input_masks
    input_files = args.files
    output_dir = args.output

    # Process the folder
    files = process(images_dir, masks_dir, input_files, output_dir)

    # Generate and print JSON result
    result = get_json({
        'status': 'success',
        'files': files,
    })
    print(result)
