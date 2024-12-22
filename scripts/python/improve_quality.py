import argparse
import os
import shutil
import tempfile
from utils.json_wrapper import get_json
import subprocess


def parse_arguments():
    """
    Parses command-line arguments.
    """
    parser = argparse.ArgumentParser(description="Process manga chapters.")
    parser.add_argument('--input', type=str, required=True, help="Path to input folder.")
    parser.add_argument('--output', type=str, required=True, help="Path to output folder.")
    return parser.parse_args()


def process_files(input_dir, output_dir):
    """
    Process files using waifu2x.
    """
    command = [
        "python", "-m", "waifu2x.cli",
        "--tune", "animation",
        "--style", "art",
        "-n", "3",
        "-m", "noise_scale2x",
        "-i", input_dir,
        "-o", output_dir,
        "--disable-exif-transpose"
    ]
    subprocess.run(command, check=True, text=True)


if __name__ == "__main__":
    # Parse arguments
    args = parse_arguments()

    # Extract arguments
    input_dir = args.input
    output_dir = args.output

    os.makedirs(os.path.dirname(output_dir), exist_ok=True)

    # Handle same input and output directory case
    if os.path.abspath(input_dir) == os.path.abspath(output_dir):
        with tempfile.TemporaryDirectory() as temp_dir:
            # Process files to the temporary directory
            process_files(input_dir, temp_dir)

            # Replace files in the output directory
            for root, _, files in os.walk(temp_dir):
                for file in files:
                    src_path = os.path.join(root, file)
                    dest_path = os.path.join(output_dir, os.path.relpath(src_path, temp_dir))
                    shutil.move(src_path, dest_path)
    else:
        # Process files directly to the output directory
        process_files(input_dir, output_dir)

    # Generate and print JSON result
    result = get_json({
        'status': 'success',
    })
    print(result)
