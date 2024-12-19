# bubble_mask.py

import argparse
import os
from improve_quality.predict import process_folder
from utils.json_wrapper import get_json


def parse_arguments():
    """
    Parses command-line arguments.
    """
    parser = argparse.ArgumentParser(description="Process manga chapters.")
    parser.add_argument('--input', type=str, required=True, help="Path to input folder.")
    parser.add_argument('--output', type=str, required=True, help="Path to output folder.")
    return parser.parse_args()


if __name__ == "__main__":
    # Parse arguments
    args = parse_arguments()

    # Extract arguments
    input_dir = args.input
    output_dir = args.output

    # Process the folder
    # process_folder(input_dir, input_files, output_dir)

    f"python -m waifu2x.cli --tune animation --style art -n 3 -m noise_scale2x -i {input_dir} -o {output_dir}"

    # Generate and print JSON result
    result = get_json({
        'status': 'success',
    })
    print(result)
