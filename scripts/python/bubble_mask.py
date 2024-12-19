# bubble_mask.py

import argparse
import os
from mask.bubble import process_folder
from utils.json_wrapper import get_json


def parse_arguments():
    """
    Parses command-line arguments.
    """
    parser = argparse.ArgumentParser(description="Process manga chapters.")
    parser.add_argument('--input', type=str, required=True, help="Path to input folder.")
    parser.add_argument('--files', type=str, required=True, help="Input files.")
    parser.add_argument('--output', type=str, required=True, help="Path to output folder.")
    return parser.parse_args()


if __name__ == "__main__":
    # Parse arguments
    args = parse_arguments()

    # Extract arguments
    input_dir = args.input
    input_files = args.files
    output_dir = args.output

    # Process the folder
    files = process_folder(input_dir, input_files, output_dir)

    # Generate and print JSON result
    result = get_json({
        'status': 'success',
        'files': files,
    })
    print(result)
