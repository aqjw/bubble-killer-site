# split.py

import argparse
import sys
import os
from split.splitter import process_folder
from utils.json_wrapper import get_json

sys.path.append(os.path.abspath(os.path.dirname(__file__)))

def parse_arguments():
    """
    Parses command-line arguments.
    """
    parser = argparse.ArgumentParser(description="Process manga chapters.")
    parser.add_argument('--input', type=str, required=True, help="Path to input folder.")
    parser.add_argument('--output', type=str, required=True, help="Path to output folder.")
    parser.add_argument('--min_width', type=int, default=100, help="Minimum width for saving segment.")
    parser.add_argument('--min_height', type=int, default=100, help="Minimum height for saving segment.")
    return parser.parse_args()


if __name__ == "__main__":
    # Parse arguments
    args = parse_arguments()

    # Extract arguments
    dir_input = args.input
    dir_output = args.output
    min_dimensions = (args.min_width, args.min_height)

    # Process the folder
    files = process_folder(dir_input, dir_output, min_dimensions)

    # Generate and print JSON result
    result = get_json({
        'status': 'success',
        'files': files,
    })
    print(result)
